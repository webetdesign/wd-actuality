<?php

// Standalone regression for A2lix TranslationForm 4: no connection, DDL or application side effects.
// php tests/a2lix-4-translation-forms.php <IMM-application-root>
declare(strict_types=1);

use A2lix\TranslationFormBundle\Form\Extension\LocaleExtension;
use A2lix\TranslationFormBundle\Form\Type\TranslationsFormsType;
use A2lix\TranslationFormBundle\LocaleProvider\LocaleProviderInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Forms;
use Symfony\Component\OptionsResolver\OptionsResolver;

$root = $argv[1] ?? '';
if (!is_file($root . '/vendor/autoload.php')) {
    throw new RuntimeException('Supply the IMM application root for this cross-repository integration test');
}
$loader = require $root . '/vendor/autoload.php';
$source = dirname(__DIR__) . '/src';
$loader->addPsr4('WebEtDesign\\ActualityBundle\\', $source, true);

/** Same fields as ActualityContentTranslationType, without the CKEditor widget (needs the application container). */
final class PlainContentTranslationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('excerpt', TextareaType::class)->add('content', TextareaType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => 'App\\Entity\\Actuality\\ActualityTranslation']);
    }
}

$failures = [];
$check = static function (bool $ok, string $label) use (&$failures): void {
    echo ($ok ? 'PASS ' : 'FAIL ') . $label . PHP_EOL;
    if (!$ok) { $failures[] = $label; }
};

// 1. Admin forms must use the A2lix 4 option names (a stale "locales" option is rejected at runtime).
foreach (['ActualityAdmin', 'CategoryAdmin'] as $admin) {
    $code = (string) file_get_contents($source . '/Admin/' . $admin . '.php');
    $check(!str_contains($code, "'locales'"), $admin . ' no longer passes the removed "locales" option');
    $check(str_contains($code, "'enabled_locales'"), $admin . ' passes "enabled_locales"');
    $check(str_contains($code, "'getter'"), $admin . ' reads translations by locale key');
}

// 2. Real submission through A2lix 4 with the bundle translation types and IMM concrete entities.
$provider = new class implements LocaleProviderInterface {
    public function getEnabledLocales(): array { return ['fr', 'en']; }
    public function getDefaultLocale(): string { return 'fr'; }
    public function getRequiredLocales(): array { return ['fr']; }
};
$factory = Forms::createFormFactoryBuilder()
    ->addType(new TranslationsFormsType())
    ->addTypeExtension(new LocaleExtension($provider))
    ->getFormFactory();

$cases = [
    'ActualityTitleTranslationType' => ['title' => 'Synthetic title'],
    'CategoryTitleTranslationType' => ['title' => 'Synthetic category'],
    'ActualityContentTranslationType' => ['excerpt' => 'Synthetic excerpt', 'content' => 'Synthetic content'],
];
foreach ($cases as $short => $values) {
    $type = 'WebEtDesign\\ActualityBundle\\Form\\Admin\\' . $short;
    try {
        $getter = static fn ($data, $form) => $data->get($form->getName());
        // CKEditorType is not registered in this minimal factory: the content case uses a plain equivalent.
        $formType = $short === 'ActualityContentTranslationType' ? PlainContentTranslationType::class : $type;
        $options = ['form_type' => $formType, 'form_options' => ['getter' => $getter]];
        $form = $factory->create(TranslationsFormsType::class, new ArrayCollection(), $options);
        $form->submit(['fr' => $values, 'en' => $values]);
        $data = $form->getData();
        $translation = $data->get('en');
        $check($form->isSynchronized() && $data->count() === 2, $short . ' submits both locales');
        $check($translation->getLocale() === 'en', $short . ' stores the locale through __set');
    } catch (Throwable $e) {
        $check(false, $short . ' submission: ' . get_class($e) . ': ' . substr($e->getMessage(), 0, 140));
    }
}

// 3. Translation entities satisfy the A2lix 4 contract.
foreach (['App\\Entity\\Actuality\\ActualityTranslation', 'App\\Entity\\Actuality\\CategoryTranslation'] as $class) {
    $check(is_subclass_of($class, 'A2lix\\TranslationFormBundle\\Helper\\OneLocaleInterface'), $class . ' implements OneLocaleInterface');
}

exit($failures === [] ? 0 : 1);
