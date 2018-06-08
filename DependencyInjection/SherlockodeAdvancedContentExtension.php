<?php

namespace Sherlockode\AdvancedContentBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

/**
 * Class SherlockodeAdvancedContentExtension
 */
class SherlockodeAdvancedContentExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');

        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);
        $configurationManager = $container->getDefinition('sherlockode_advanced_content.configuration_manager');
        $configurationManager->addMethodCall('setConfig', [$config]);
    }
}
