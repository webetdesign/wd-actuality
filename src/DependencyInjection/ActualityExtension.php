<?php

namespace WebEtDesign\ActualityBundle\DependencyInjection;

use Sonata\Doctrine\Mapper\Builder\OptionsBuilder;
use Sonata\Doctrine\Mapper\DoctrineCollector;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use WebEtDesign\ActualityBundle\Entity\Actuality;

class ActualityExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {

        $configuration = new Configuration();
        $processor     = new Processor();
        $config        = $processor->processConfiguration($configuration, $configs);

        $this->configureClass($config, $container);

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yaml');
        $loader->load('admin.yaml');

        $container->setParameter('wd_actuality.config', $config['configuration']);

    }

    /**
     * @param array $config
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    public function configureClass($config, ContainerBuilder $container)
    {
        // manager configuration
        $container->setParameter('wd_actuality.admin.content.user', $config['class']['user']);
        $container->setParameter('wd_actuality.admin.content.media', $config['class']['media']);
    }

    public function getAlias()
    {
        return 'wd_actuality';
    }


}
