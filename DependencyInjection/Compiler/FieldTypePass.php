<?php

namespace Sherlockode\AdvancedContentBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;
use Sherlockode\AdvancedContentBundle\Manager\FieldManager;

class FieldTypePass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->has(FieldManager::class)) {
            return;
        }
        $definition = $container->findDefinition(FieldManager::class);
        $taggedServices = $container->findTaggedServiceIds('sherlockode_advanced_content.fieldtype');
        foreach ($taggedServices as $id => $tags) {
            $definition->addMethodCall('addFieldType', [new Reference($id)]);
        }
    }
}
