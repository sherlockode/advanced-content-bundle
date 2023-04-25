<?php

namespace Sherlockode\AdvancedContentBundle\Scope;

use Sherlockode\AdvancedContentBundle\Model\ContentInterface;
use Sherlockode\AdvancedContentBundle\Model\PageInterface;
use Sherlockode\AdvancedContentBundle\Model\ScopableInterface;
use Sherlockode\AdvancedContentBundle\Model\ScopeInterface;

interface ScopeHandlerInterface
{
    /**
     * @return string|null
     */
    public function getScopeGroupBy(): ?string;

    /**
     * @param ContentInterface $content
     *
     * @return bool
     */
    public function isContentSlugValid(ContentInterface $content): bool;

    /**
     * @param PageInterface $page
     *
     * @return bool
     */
    public function isPageSlugValid(PageInterface $page): bool;

    /**
     * @param PageInterface $page
     *
     * @return bool
     */
    public function isPageIdentifierValid(PageInterface $page): bool;

    /**
     * @param array $data
     *
     * @return ScopeInterface|null
     */
    public function getScopeFromData(array $data): ?ScopeInterface;

    /**
     * @param ScopeInterface $scope
     *
     * @return array
     */
    public function getDataFromScope(ScopeInterface $scope): array;

    /**
     * @param string $entityCode
     * @param array  $criteria
     *
     * @return ScopableInterface|null
     */
    public function getEntityForCurrentScope(string $entityCode, array $criteria): ?ScopableInterface;

    /**
     * @param array|ScopableInterface[] $entities
     *
     * @return ScopableInterface|null
     */
    public function filterEntityForCurrentScope(array $entities): ?ScopableInterface;

    /**
     * @return ScopeInterface|null
     */
    public function getCurrentScope(): ?ScopeInterface;
}
