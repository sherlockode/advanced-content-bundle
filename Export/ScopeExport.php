<?php

namespace Sherlockode\AdvancedContentBundle\Export;

use Sherlockode\AdvancedContentBundle\Manager\ConfigurationManager;
use Sherlockode\AdvancedContentBundle\Model\ScopableInterface;
use Sherlockode\AdvancedContentBundle\Scope\ScopeHandlerInterface;

class ScopeExport
{
    public function __construct(
        private readonly ConfigurationManager $configurationManager,
        private readonly ScopeHandlerInterface $scopeHandler,
    ) {
    }

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
