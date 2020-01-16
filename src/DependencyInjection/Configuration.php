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
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('wd_actuality');

        $rootNode
            ->children()
                ->arrayNode('class')
                    ->children()
                        ->scalarNode('user')->cannotBeEmpty()->end()
                        ->scalarNode('media')->cannotBeEmpty()->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
