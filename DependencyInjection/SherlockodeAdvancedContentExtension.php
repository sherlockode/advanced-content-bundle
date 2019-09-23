<?php

namespace Sherlockode\AdvancedContentBundle\DependencyInjection;

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

        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);
        $configurationManager = $container->getDefinition('sherlockode_advanced_content.configuration_manager');
        $configurationManager->addMethodCall('setConfig', [$config]);

        $container->setParameter('sherlockode_advanced_content.entity_class_mapping', $config['entity_class']);

        $targetDir = $config['upload']['image_directory'] ?? sys_get_temp_dir();
        $webPath = $config['upload']['uri_prefix'] ?? '/';

        $uploadManager = $container->getDefinition('sherlockode_advanced_content.upload_manager');
        $uploadManager->setArguments([
            $targetDir,
            $webPath,
        ]);

        $container->setParameter('sherlockode_advanced_content.templates.tools', $config['templates']['tools']);
    }
}
