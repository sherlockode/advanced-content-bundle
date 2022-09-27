<?php

namespace Sherlockode\AdvancedContentBundle\DependencyInjection;

use Sherlockode\AdvancedContentBundle\FieldType\FieldTypeInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

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
        $loader->load('form.xml');
        $loader->load('import_export.xml');
        $loader->load('listeners.xml');

        $container->registerForAutoconfiguration(FieldTypeInterface::class)
            ->addTag('sherlockode_advanced_content.fieldtype');

        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);
        $configurationManager = $container->getDefinition('sherlockode_advanced_content.configuration_manager');
        $configurationManager->addMethodCall('setConfig', [$config]);

        $container->setParameter('sherlockode_advanced_content.entity_class_mapping', $config['entity_class']);

        $this->setupUploads($config, $container);

        $container->getDefinition('sherlockode_advanced_content.field_manager')->setArgument(0, $config['field_types']);

        $localeProvider = $container->getDefinition('sherlockode_advanced_content.locale_provider');
        $localeProvider->setArgument(0, $config['locales']);

        $container->setParameter('sherlockode_advanced_content.templates.tools', $config['templates']['tools']);
    }

    protected function setupUploads(array $config, ContainerBuilder $container)
    {
        $targetDir = $config['upload']['image_directory'] ?? sys_get_temp_dir();
        $webPath = $config['upload']['uri_prefix'] ?? '/';

        $uploadManager = $container->getDefinition('sherlockode_advanced_content.upload_manager');
        $uploadManager->setArguments([
            $targetDir,
            $webPath,
        ]);
    }
}
