<?php

namespace Sherlockode\AdvancedContentBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class FieldTypePass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->has('sherlockode_advanced_content.field_manager')) {
            return;
        }
        $definition = $container->findDefinition('sherlockode_advanced_content.field_manager');
        $taggedServices = $container->findTaggedServiceIds('sherlockode_advanced_content.fieldtype');
        foreach ($taggedServices as $id => $tags) {
            $definition->addMethodCall('addFieldType', [new Reference($id)]);
        }
    }
}
