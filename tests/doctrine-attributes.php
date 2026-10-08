<?php

// Standalone metadata regression: no connection, DDL or application side effects.
// php tests/doctrine-attributes.php <IMM-application-root> [ORM2-snapshot.json]
declare(strict_types=1);

use Doctrine\Common\EventManager;
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\Driver\AttributeDriver;
use Doctrine\ORM\ORMSetup;
use Doctrine\Persistence\Mapping\Driver\MappingDriverChain;
use Knp\DoctrineBehaviors\Contract\Provider\LocaleProviderInterface;
use Knp\DoctrineBehaviors\EventSubscriber\TranslatableEventSubscriber;

$root = $argv[1] ?? '';
if (!is_file($root . '/vendor/autoload.php')) { throw new RuntimeException('Supply the IMM application root for this cross-repository integration test'); }
$loader = require $root . '/vendor/autoload.php';
$source = dirname(__DIR__) . '/src';
$baseline = in_array('--baseline', $argv, true);
$loader->setPsr4('WebEtDesign\\ActualityBundle\\', $source);
if ($baseline && getenv('IMM_BASELINE_APP')) {
    $loader->setPsr4('App\\Entity\\Actuality\\', getenv('IMM_BASELINE_APP'));
}
$config = ORMSetup::createAttributeMetadataConfiguration([], true);
$drivers = new MappingDriverChain();
$attributes = new AttributeDriver([$root . '/src/Entity', $source . '/Entity']);
if ($baseline) {
    $annotations = new Doctrine\ORM\Mapping\Driver\AnnotationDriver(new Doctrine\Common\Annotations\AnnotationReader(), []);
    $drivers->addDriver($annotations, 'App\\Entity\\Actuality');
    $drivers->addDriver($annotations, 'App\\Entity\\Reference\\Delivery');
    $drivers->addDriver($annotations, 'WebEtDesign\\ActualityBundle');
} else {
    $drivers->addDriver($attributes, 'App\\Entity\\Actuality');
    $drivers->addDriver($attributes, 'WebEtDesign\\ActualityBundle');
}
$drivers->addDriver($attributes, 'App\\Entity');
foreach (['MediaBundle', 'SeoBundle'] as $namespace) {
    $drivers->addDriver($attributes, 'WebEtDesign\\' . $namespace);
}
$config->setMetadataDriverImpl($drivers);
$config->setNamingStrategy(new Doctrine\ORM\Mapping\UnderscoreNamingStrategy());
$events = new EventManager();
if ($baseline) {
    // The legacy bundle mixes annotated properties with already attributed SEO
    // traits. Capture both declared mappings; annotation-only omits those traits.
    $events->addEventListener(['loadClassMetadata'], new class {
        public function loadClassMetadata(Doctrine\ORM\Event\LoadClassMetadataEventArgs $event): void
        {
            $metadata = $event->getClassMetadata();
            if (getenv('IMM_METADATA_TRACE')) { fwrite(STDERR, $metadata->name . ' ' . $metadata->getReflectionClass()->getFileName() . PHP_EOL); }
            if ($metadata->name !== 'WebEtDesign\\ActualityBundle\\Entity\\WDActuality') { return; }
            $attributed = new Doctrine\ORM\Mapping\ClassMetadata($metadata->name, new Doctrine\ORM\Mapping\UnderscoreNamingStrategy());
            (new AttributeDriver([], true))->loadMetadataForClass($metadata->name, $attributed);
            foreach ($attributed->fieldMappings as $field => $mapping) {
                if (!in_array($field, ['createdAt', 'updatedAt'], true) && !$metadata->hasField($field)) { $metadata->mapField($mapping); }
            }
            foreach ($attributed->associationMappings as $field => $mapping) {
                if (!$metadata->hasAssociation($field)) { $metadata->mapManyToOne($mapping); }
            }
        }
    });
}
$locale = new class implements LocaleProviderInterface {
    public function provideCurrentLocale(): ?string { return 'fr'; }
    public function provideFallbackLocale(): ?string { return 'en'; }
};
$events->addEventListener(['loadClassMetadata'], new TranslatableEventSubscriber($locale, 'LAZY', 'LAZY'));
$connection = DriverManager::getConnection(['driver' => 'pdo_sqlite', 'memory' => true]);
$em = new EntityManager($connection, $config, $events);
$checks = 0;
$check = static function (bool $value, string $message) use (&$checks): void {
    ++$checks;
    if (!$value) { throw new RuntimeException($message); }
};
$normalise = static function ($value) use (&$normalise) {
    if ($value instanceof UnitEnum && get_class($value) === 'SortDirection') {
        return $value->name === 'Ascending' ? 'ASC' : 'DESC';
    }
    if (is_object($value)) { $value = get_object_vars($value); }
    if (is_array($value)) {
        // Driver bookkeeping is not a column/association contract.
        unset($value['declared'], $value['inherited']);
        foreach ($value as $key => $item) { $value[$key] = $normalise($item); }
        if (!array_is_list($value)) { ksort($value); }
    }
    return $value;
};
try {
    $snapshot = [];
    $tables = ['Actuality' => 'actuality__actuality', 'ActualityMedia' => 'actuality__actuality_media', 'ActualityTranslation' => 'actuality__actuality_translation', 'Category' => 'actuality__category', 'CategoryTranslation' => 'actuality__category_translation'];
    foreach ($tables as $name => $table) {
        $class = 'App\\Entity\\Actuality\\' . $name;
        $metadata = $em->getClassMetadata($class);
        $check(!$metadata->isMappedSuperclass, $class . ' must remain a concrete mapped entity');
        $check($metadata->getTableName() === $table, $class . ': table changed');
        $check($metadata->getIdentifierFieldNames() === ['id'], $class . ': identifier missing');
        $snapshot[$class] = $normalise(['table' => $metadata->table, 'fields' => $metadata->fieldMappings, 'associations' => $metadata->associationMappings, 'repository' => $metadata->customRepositoryClassName, 'generator' => $metadata->generatorType]);
    }
    $actuality = $em->getClassMetadata('App\\Entity\\Actuality\\Actuality');
    $category = $em->getClassMetadata('App\\Entity\\Actuality\\Category');
    $media = $em->getClassMetadata('App\\Entity\\Actuality\\ActualityMedia');
    $check($category->hasField('position'), 'Category inherited sortable position missing');
    $check($category->getAssociationTargetClass('actualities') === $actuality->name, 'Category / Actuality association changed');
    $check($actuality->getAssociationTargetClass('category') === $category->name, 'Actuality / Category association changed');
    $pictures = $normalise($actuality->getAssociationMapping('pictures'));
    $check($pictures['orderBy'] === ['position' => 'ASC'], 'Picture position ordering lost: ' . json_encode($pictures));
    $check($pictures['cascade'] === ['persist', 'remove'], 'Picture cascade changed');
    $check($media->hasField('position'), 'Media inherited position missing');
    $check($media->getAssociationTargetClass('media') === 'WebEtDesign\\MediaBundle\\Entity\\Media', 'Media relation lost');
    foreach (['Actuality', 'Category'] as $name) {
        $metadata = $em->getClassMetadata('App\\Entity\\Actuality\\' . $name);
        $translations = $normalise($metadata->getAssociationMapping('translations'));
        $translation = $em->getClassMetadata('App\\Entity\\Actuality\\' . $name . 'Translation');
        $check($translations['targetEntity'] === $translation->name, $name . ': translation entity changed');
        $check($translations['indexBy'] === 'locale' && $translations['orphanRemoval'], $name . ': multilingual collection contract changed');
        $check($translation->hasField('locale') && $translation->hasField('slug') && $translation->hasField('title'), $name . ': multilingual fields lost');
        $check($translation->getAssociationTargetClass('translatable') === $metadata->name, $name . ': translation back-reference lost');
        $constraints = $translation->table['uniqueConstraints'];
        $check($constraints[$translation->getTableName() . '_unique_translation']['columns'] === ['translatable_id', 'locale'], $name . ': unique locale constraint lost');
    }
    if (!$baseline) {
        $slug = (new ReflectionProperty('WebEtDesign\\ActualityBundle\\Entity\\WDActualityTranslation', 'slug'))->getAttributes(Gedmo\Mapping\Annotation\Slug::class);
        $check(count($slug) === 1 && $slug[0]->newInstance()->fields === ['title'], 'Gedmo slug attribute missing');
        $position = (new ReflectionProperty('WebEtDesign\\ActualityBundle\\Entity\\WDCategory', 'position'))->getAttributes(Gedmo\Mapping\Annotation\SortablePosition::class);
        $check(count($position) === 1, 'Gedmo sortable position attribute missing');
        $position[0]->newInstance();
        $callback = (new ReflectionMethod('WebEtDesign\\ActualityBundle\\Entity\\WDActuality', 'validate'))->getAttributes(Symfony\Component\Validator\Constraints\Callback::class);
        $check(count($callback) === 1, 'Publication validation callback missing');
    }
    $snapshotPath = $argv[$baseline ? 3 : 2] ?? null;
    if (getenv('IMM_METADATA_DUMP')) {
        file_put_contents(getenv('IMM_METADATA_DUMP'), json_encode($snapshot, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR));
    }
    if ($snapshotPath) {
        if ($baseline) {
            file_put_contents($snapshotPath, json_encode($snapshot, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR));
        } else {
            $expected = json_decode(file_get_contents($snapshotPath), true, 512, JSON_THROW_ON_ERROR);
            $check($snapshot === $expected, 'Actuality complete metadata differs from annotation baseline');
        }
    }
    $check(!$connection->isConnected(), 'Metadata test must not open a database connection');
    echo 'PASS Actuality metadata: ' . $checks . ' assertions; no database connection.' . PHP_EOL;
} catch (Throwable $error) {
    fwrite(STDERR, 'FAIL after ' . $checks . ' assertions: ' . $error->getMessage() . PHP_EOL);
    exit(1);
}
