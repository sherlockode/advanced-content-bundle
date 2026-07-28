<?php

declare(strict_types=1);

namespace Sherlockode\AdvancedContentBundle\Twig\Extension;

use Sherlockode\AdvancedContentBundle\Manager\ConfigurationManager;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class ScopeExtension extends AbstractExtension
{
    public function __construct(private readonly ConfigurationManager $configurationManager)
    {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('acb_is_scopes_enabled', [$this, 'isScopesEnabled']),
        ];
    }

    public function isScopesEnabled(): bool
    {
        return $this->configurationManager->isScopesEnabled();
    }
}
