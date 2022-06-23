<?php
/**
 * Created by PhpStorm.
 * User: jvaldena
 * Date: 22/01/2019
 * Time: 16:27
 */

namespace WebEtDesign\ActualityBundle\DependencyInjection;


use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\HttpFoundation\Request;
use WebEtDesign\CmsBundle\Entity\CmsGlobalVarsDelimiterEnum;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('wd_actuality');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
                ->arrayNode('class')
                    ->children()
                        ->scalarNode('user')->cannotBeEmpty()->end()
                        ->scalarNode('media')->cannotBeEmpty()->end()
                    ->end()
                ->end()
                ->arrayNode('seo')->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('actuality_route_name')->defaultValue('actuality')->end()
                        ->scalarNode('category_route_name')->defaultValue('category_actuality')->end()
                        ->scalarNode('host')->defaultNull()->end()
                        ->scalarNode('scheme')->defaultNull()->end()
                        ->floatNode('priority')->min(0)->max(1)->end()
                        ->enumNode('changefreq')
                            ->values(['always', 'hourly', 'daily', 'weekly', 'monthly', 'yearly', 'never'])
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('configuration')->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('result_limit')->defaultValue(9)->end()
                        ->scalarNode('use_category')->defaultValue(true)->end()
                        ->scalarNode('generate_sitemap')->defaultValue(true)->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
