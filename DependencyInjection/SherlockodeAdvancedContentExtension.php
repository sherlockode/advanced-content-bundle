<?php

namespace Sherlockode\AdvancedContentBundle\DependencyInjection;

use Sherlockode\AdvancedContentBundle\FieldType\FieldTypeInterface;
use Sherlockode\AdvancedContentBundle\LayoutType\LayoutTypeInterface;
use Sherlockode\AdvancedContentBundle\Naming\NamerInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class SherlockodeAdvancedContentExtension
 */
class SherlockodeAdvancedContentExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.xml');
        $loader->load('controllers.xml');
        $loader->load('field_types.xml');
        $loader->load('layout_types.xml');
        $loader->load('form.xml');
        $loader->load('import_export.xml');
        $loader->load('listeners.xml');
        $loader->load('mime_type.xml');

        $container->registerForAutoconfiguration(FieldTypeInterface::class)
            ->addTag('sherlockode_advanced_content.fieldtype');
        $container->registerForAutoconfiguration(LayoutTypeInterface::class)
            ->addTag('sherlockode_advanced_content.layouttype');

        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);
        $configurationManager = $container->getDefinition('sherlockode_advanced_content.configuration_manager');
        $configurationManager->addMethodCall('setConfig', [$config]);

        $container->setParameter('sherlockode_advanced_content.entity_class_mapping', $config['entity_class']);

        $this->setupUploads($config, $container);
        $this->setupMimeType($config, $container);

        $container->getDefinition('sherlockode_advanced_content.element_manager')->setArgument(0, $config['field_types']);

        $container->setParameter('sherlockode_advanced_content.templates.tools', $config['templates']['tools']);
    }

    protected function setupUploads(array $config, ContainerBuilder $container)
    {
        $targetDir = $config['upload']['image_directory'] ?? sys_get_temp_dir();
        $webPath = $config['upload']['uri_prefix'] ?? '/';

        $uploadManager = $container->getDefinition('sherlockode_advanced_content.upload_manager');
        $uploadManager->setArguments([
            new Reference($config['upload']['file_namer']),
            $targetDir,
            $webPath,
        ]);
    }

    /**
     * @param array            $config
     * @param ContainerBuilder $container
     *
     * @return void
     */
    private function setupMimeType(array $config, ContainerBuilder $container): void
    {
        $mimeTypesConfiguration = $config['mime_type_group'] ?? null;
        if (null === $mimeTypesConfiguration) {
            return;
        }

        $parameters = $container->getParameter('sherlockode_advanced_content.mime_type_group');

        foreach ($mimeTypesConfiguration as $key => $mimeTypeConfiguration) {
            if (isset($parameters['sherlockode_advanced_content.mime_type_group.'.$key])) {
                $parameters['sherlockode_advanced_content.mime_type_group.'.$key] = $mimeTypeConfiguration;
            }
        }

        $container->setParameter('sherlockode_advanced_content.mime_type_group', $parameters);
    }
}
