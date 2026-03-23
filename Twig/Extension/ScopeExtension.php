<?php

declare(strict_types=1);

namespace Sherlockode\AdvancedContentBundle\Twig\Extension;

use Twig\Attribute\AsTwigFunction;
use Sherlockode\AdvancedContentBundle\Manager\ConfigurationManager;

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
