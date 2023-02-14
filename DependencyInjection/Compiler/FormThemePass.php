<?php

namespace Sherlockode\AdvancedContentBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\HttpKernel\Kernel;

class FormThemePass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->has('twig')) {
            return;
        }

        $theme = 'bootstrap_5_layout.html.twig';
        if (Kernel::VERSION_ID < 50300) {
            $theme = 'bootstrap_4_layout.html.twig';
        }
        $container->getDefinition('sherlockode_advanced_content.content_extension')->setArgument('$baseFormTheme', $theme);
    }
}
