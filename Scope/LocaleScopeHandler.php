<?php

namespace Sherlockode\AdvancedContentBundle\Scope;

class LocaleScopeHandler extends ScopeHandler
{
    /**
     * @return string|null
     */
    public function getScopeGroupBy(): ?string
    {
        return null;
    }

    /**
     * @param array $data
     *
     * @return ScopeInterface|null
     */
    public function getScopeFromData(array $data): ?ScopeInterface
    {
        if (empty($data['locale'])) {
            return null;
        }

        return $this->em->getRepository($this->configurationManager->getEntityClass('scope'))->findOneBy([
            'locale' => $data['locale'],
        ]);
    }

    /**
     * @param ScopeInterface $scope
     *
     * @return array
     */
    public function getDataFromScope(ScopeInterface $scope): array
    {
        return [
            'locale' => $scope->getLocale(),
        ];
    }
}
