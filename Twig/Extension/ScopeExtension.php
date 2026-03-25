<?php

namespace Sherlockode\AdvancedContentBundle\Twig\Extension;

use Sherlockode\AdvancedContentBundle\Manager\ConfigurationManager;
use Twig\Attribute\AsTwigFunction;

class ScopeExtension
{
    public function __construct(private readonly ConfigurationManager $configurationManager)
    {
    }

    #[AsTwigFunction(name: 'acb_is_scopes_enabled')]
    public function isScopesEnabled(): bool
    {
        return $this->configurationManager->isScopesEnabled();
    }
}
