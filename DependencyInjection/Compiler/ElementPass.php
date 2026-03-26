<?php

declare(strict_types=1);

namespace Sherlockode\AdvancedContentBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class ElementPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->has('sherlockode_advanced_content.element_manager')) {
            return;
        }

        $definition = $container->findDefinition('sherlockode_advanced_content.element_manager');
        $taggedServices = $container->findTaggedServiceIds('sherlockode_advanced_content.fieldtype');
        foreach (array_keys($taggedServices) as $id) {
            $definition->addMethodCall('addFieldType', [new Reference($id)]);
        }

        $taggedServices = $container->findTaggedServiceIds('sherlockode_advanced_content.layouttype');
        foreach (array_keys($taggedServices) as $id) {
            $definition->addMethodCall('addLayoutType', [new Reference($id)]);
        }
    }
}
