<?php

// Cross-repository source/DI regression; no kernel, connection or vendor writes.
// php tests/symfony-sonata-contracts.php <IMM-application-root> [--services]
declare(strict_types=1);

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Symfony\Component\Yaml\Yaml;

$root = $argv[1] ?? '';
$loader = require $root . '/vendor/autoload.php';
$source = dirname(__DIR__) . '/src';
$loader->setPsr4('WebEtDesign\\ActualityBundle\\', $source);
$failures = [];
$check = static function (bool $ok, string $label) use (&$failures): void {
    echo ($ok ? 'PASS ' : 'FAIL ') . $label . PHP_EOL;
    if (!$ok) { $failures[] = $label; }
};

if (!in_array('--services', $argv, true)) {
    // Inspect source before autoload: PHP's incompatible inheritance error is not catchable.
    foreach (['ActualityTitleTranslationType', 'CategoryTitleTranslationType', 'ActualityContentTranslationType'] as $short) {
        $code = (string) file_get_contents($source . '/Form/Admin/' . $short . '.php');
        foreach (['buildForm', 'configureOptions'] as $method) {
            $check(preg_match('/public function ' . $method . '\([^)]*\)\s*:\s*void\b/', $code) === 1, $short . '::' . $method . ' declares void');
        }
    }
    if ($failures === []) {
        foreach (['ActualityTitleTranslationType', 'CategoryTitleTranslationType', 'ActualityContentTranslationType'] as $short) {
            $class = 'WebEtDesign\\ActualityBundle\\Form\\Admin\\' . $short;
            foreach (['buildForm', 'configureOptions'] as $method) {
                $check((string) (new ReflectionMethod($class, $method))->getReturnType() === 'void', $short . '::' . $method . ' loads with Symfony inheritance');
            }
        }
    }
} else {
    $config = Yaml::parseFile($source . '/Resources/config/admin.yaml');
    $container = new ContainerBuilder();
    (new YamlFileLoader($container, new FileLocator($source . '/Resources/config')))->load('admin.yaml');
    $bag = new ParameterBag(['wd_actuality.config' => ['use_category' => true], 'wd_actuality.translation.locales' => ['fr', 'en'], 'wd_actuality.translation.default_locale' => 'fr']);
    $container->set('parameter_bag', $bag);
    foreach (['actuality' => 'Actuality', 'category' => 'Category'] as $name => $entity) {
        $id = 'wd_actuality.admin.' . $name;
        $service = $config['services'][$id];
        $tag = $service['tags'][0];
        $check(($service['arguments'] ?? []) === [], $id . ' has no legacy constructor arguments');
        $check(($tag['model_class'] ?? null) === 'App\\Entity\\Actuality\\' . $entity, $id . ' declares its model_class');
        $check(($tag['code'] ?? $id) === $id && !isset($tag['controller']), $id . ' preserves service code and configured default controller');
        $deprecations = [];
        set_error_handler(static function (int $severity, string $message) use (&$deprecations): bool {
            if ($severity === E_USER_DEPRECATED) { $deprecations[] = $message; return true; }
            return false;
        });
        try { $admin = $container->get($id); } finally { restore_error_handler(); }
        $check($deprecations === [], $id . ' constructs without Sonata deprecation: ' . implode('; ', $deprecations));
        // Only exercise the tag's identity setters here. Full Sonata compiler integration is separate.
        if (isset($tag['model_class'])) {
            $admin->setModelClass($tag['model_class']);
            $admin->setCode($tag['code'] ?? $id);
            $admin->setBaseControllerName('Sonata\\AdminBundle\\Controller\\CRUDController');
        }
        $check($admin->getModelClass() === 'App\\Entity\\Actuality\\' . $entity, $id . ' preserves model identity');
        $check((new ReflectionProperty($admin, 'parameterBag'))->getValue($admin) === $bag, $id . ' retains setParameterBag injection');
        $check((new ReflectionProperty($admin, 'locales'))->getValue($admin) === ['fr', 'en'], $id . ' receives locales');
        $check((new ReflectionProperty($admin, 'defaultLocale'))->getValue($admin) === 'fr', $id . ' receives default locale');
        $check($admin->getTranslationDomain() === ($name === 'actuality' ? 'actuality__actuality_admin' : 'actuality__catgeory_admin'), $id . ' preserves translation domain');
    }
}
exit($failures === [] ? 0 : 1);
