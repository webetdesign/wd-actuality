<?php

use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('wd_actuality');

        $rootNode
            ->children()
                ->arrayNode('parameters')->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('param')->defaultValue(null)->end()
                    ->end()
                ->end()
            ->end()
        ;
        return $treeBuilder;
    }
}
