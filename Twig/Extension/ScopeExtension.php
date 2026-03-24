<?php

namespace Sherlockode\AdvancedContentBundle\Twig\Extension;

use Sherlockode\AdvancedContentBundle\Manager\ConfigurationManager;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class ScopeExtension extends AbstractExtension
{
    /**
     * @var ConfigurationManager
     */
    private $configurationManager;

    public function __construct(
        ConfigurationManager $configurationManager,
    ) {
        $this->configurationManager = $configurationManager;
    }

    /**
     * Add specific twig function.
     *
     * @return TwigFunction[]
     */
    public function getFunctions()
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
