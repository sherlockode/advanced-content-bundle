<?php

namespace Sherlockode\AdvancedContentBundle\Scope;

use Sherlockode\AdvancedContentBundle\Model\ContentInterface;
use Sherlockode\AdvancedContentBundle\Model\PageInterface;
use Sherlockode\AdvancedContentBundle\Model\ScopableInterface;
use Sherlockode\AdvancedContentBundle\Model\ScopeInterface;

interface ScopeHandlerInterface
{
    public function getScopeGroupBy(): ?string;

    public function isContentSlugValid(ContentInterface $content): bool;

    public function isPageSlugValid(PageInterface $page): bool;

    public function isPageIdentifierValid(PageInterface $page): bool;

    public function getScopeFromData(array $data): ?ScopeInterface;

    public function getDataFromScope(ScopeInterface $scope): array;

    public function getEntityForCurrentScope(string $entityCode, array $criteria): ?ScopableInterface;

    /**
     * @param array|ScopableInterface[] $entities
     */
    public function filterEntityForCurrentScope(array $entities): ?ScopableInterface;

    public function getCurrentScope(): ?ScopeInterface;
}
