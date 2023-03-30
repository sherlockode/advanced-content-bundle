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

    /**
     * @param ConfigurationManager $configurationManager
     */
    public function __construct(
        ConfigurationManager $configurationManager
    ) {
        $this->configurationManager = $configurationManager;
    }

    /**
     * Add specific twig function
     *
     * @return TwigFunction[]
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('acb_is_scopes_enabled', [$this, 'isScopesEnabled']),
        ];
    }

    /**
     * @return bool
     */
    public function isScopesEnabled(): bool
    {
        return $this->configurationManager->isScopesEnabled();
    }
}
