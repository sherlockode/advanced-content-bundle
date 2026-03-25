<?php

declare(strict_types=1);

namespace Sherlockode\AdvancedContentBundle;

use Doctrine\Bundle\DoctrineBundle\DependencyInjection\Compiler\DoctrineOrmMappingsPass;
use Sherlockode\AdvancedContentBundle\DependencyInjection\Compiler\ElementPass;
use Sherlockode\AdvancedContentBundle\DependencyInjection\Compiler\FormThemePass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class SherlockodeAdvancedContentBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);
        $this->addRegisterMappingsPass($container);
        $container->addCompilerPass(new ElementPass());
        $container->addCompilerPass(new FormThemePass());
    }

    private function addRegisterMappingsPass(ContainerBuilder $container): void
    {
        $mappings = [
            realpath(__DIR__.'/Resources/config/doctrine-mapping') => 'Sherlockode\AdvancedContentBundle\Model',
        ];

        if (class_exists(DoctrineOrmMappingsPass::class)) {
            $container->addCompilerPass(DoctrineOrmMappingsPass::createXmlMappingDriver($mappings));
        }
    }
}
