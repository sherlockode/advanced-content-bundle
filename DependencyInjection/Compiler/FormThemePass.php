<?php

namespace Sherlockode\AdvancedContentBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

class FormThemePass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->has('twig')) {
            return;
        }

        $theme = 'form_div_layout.html.twig';
        if ($container->hasParameter('twig.form.resources')) {
            $themes = $container->getParameter('twig.form.resources');
            if (is_array($themes) && isset($themes[0])) {
                $theme = end($themes);
            }
        }
        $container->getDefinition('sherlockode_advanced_content.content_extension')->setArgument('$baseFormTheme', $theme);
    }
}
