<?php

namespace Sherlockode\AdvancedContentBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * Build config tree for bundle
     *
     * @return TreeBuilder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('sherlockode_advanced_content');

        $rootNode
            ->children()
                ->arrayNode('entity_class')
                    ->children()
                        ->scalarNode('field_value')
                            ->isRequired()
                            ->cannotBeEmpty()
                        ->end()
                        ->scalarNode('content_type')
                            ->isRequired()
                            ->cannotBeEmpty()
                        ->end()
                        ->scalarNode('content')
                            ->isRequired()
                            ->cannotBeEmpty()
                        ->end()
                        ->scalarNode('field')
                            ->isRequired()
                            ->cannotBeEmpty()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
