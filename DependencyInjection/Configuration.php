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
                    ->isRequired()
                    ->children()
                        ->scalarNode('field_value')
                            ->isRequired()
                            ->cannotBeEmpty()
                        ->end()
                        ->scalarNode('field_group_value')
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
                        ->scalarNode('layout')
                            ->isRequired()
                            ->cannotBeEmpty()
                        ->end()
                        ->scalarNode('page_type')
                            ->isRequired()
                            ->cannotBeEmpty()
                        ->end()
                        ->scalarNode('page')
                            ->isRequired()
                            ->cannotBeEmpty()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('upload')
                    ->children()
                        ->scalarNode('image_directory')
                            ->cannotBeEmpty()
                        ->end()
                        ->scalarNode('uri_prefix')
                            ->cannotBeEmpty()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('init_command')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('directory')
                            ->defaultValue('var/acb')
                        ->end()
                        ->scalarNode('files_directory')
                            ->defaultValue('var/acb/files')
                        ->end()
                        ->booleanNode('allow_update')
                            ->defaultFalse()
                        ->end()
                        ->booleanNode('field_default_required')
                            ->defaultFalse()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('templates')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('tools')
                            ->cannotBeEmpty()
                            ->defaultValue('@SherlockodeAdvancedContent/Tools/index.html.twig')
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('default_options')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('wysiwyg_toolbar')
                            ->defaultValue('basic')
                            ->validate()
                                ->ifNotInArray(['basic', 'standard', 'full'])
                                ->thenInvalid('Invalid wysiwyg toolbar option %s')
                            ->end()
                        ->end()
                        ->booleanNode('date_include_time')
                            ->defaultTrue()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
