<?php

namespace Sherlockode\AdvancedContentBundle\DependencyInjection;

use Sherlockode\AdvancedContentBundle\Entity\Content;
use Sherlockode\AdvancedContentBundle\Entity\Page;
use Sherlockode\AdvancedContentBundle\Entity\PageMeta;
use Sherlockode\AdvancedContentBundle\Entity\PageType;
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
        $tb = new TreeBuilder('sherlockode_advanced_content');
        $root = $tb->getRootNode();

        $root
            ->children()
                ->arrayNode('entity_class')
                    ->isRequired()
                    ->children()
                        ->scalarNode('content')
                            ->isRequired()
                            ->cannotBeEmpty()
                        ->end()
                        ->scalarNode('content_version')
                            ->isRequired()
                            ->cannotBeEmpty()
                        ->end()
                        ->scalarNode('scope')
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
                        ->scalarNode('page_version')
                            ->isRequired()
                            ->cannotBeEmpty()
                        ->end()
                        ->scalarNode('page_meta')
                            ->isRequired()
                            ->cannotBeEmpty()
                        ->end()
                        ->scalarNode('page_meta_version')
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
                        ->scalarNode('file_namer')
                            ->cannotBeEmpty()
                            ->defaultValue('sherlockode_advanced_content.unique_file_namer')
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
                ->arrayNode('field_types')
                    ->useAttributeAsKey('code')
                    ->arrayPrototype()
                        ->children()
                            ->booleanNode('enabled')->defaultTrue()->end()
                            ->scalarNode('icon')->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('scopes')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('enabled')
                            ->defaultFalse()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('mime_type_group')
                    ->useAttributeAsKey('name')
                    ->arrayPrototype()
                        ->scalarPrototype()->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $tb;
    }
}
