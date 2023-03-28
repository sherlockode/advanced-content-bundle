<?php

namespace Sherlockode\AdvancedContentBundle\Export;

use Sherlockode\AdvancedContentBundle\Manager\ConfigurationManager;
use Sherlockode\AdvancedContentBundle\Model\ScopableInterface;
use Sherlockode\AdvancedContentBundle\Scope\ScopeHandlerInterface;

class ScopeExport
{
    /**
     * @var ConfigurationManager
     */
    private $configurationManager;

    /**
     * @var ScopeHandlerInterface
     */
    private $scopeHandler;

    /**
     * @param ConfigurationManager  $configurationManager
     * @param ScopeHandlerInterface $scopeHandler
     */
    public function __construct(
        ConfigurationManager $configurationManager,
        ScopeHandlerInterface $scopeHandler
    ) {
        $this->configurationManager = $configurationManager;
        $this->scopeHandler = $scopeHandler;
    }

    /**
     * @param ScopableInterface $entity
     *
     * @return array
     */
    public function getEntityScopes(ScopableInterface $entity): array
    {
        if (!$this->configurationManager->isScopesEnabled()) {
            return [];
        }

        $scopes = [];
        foreach ($entity->getScopes() as $scope) {
            $scopes[] = $this->scopeHandler->getDataFromScope($scope);
        }

        return [
            'scopes' => $scopes,
        ];
    }
}
