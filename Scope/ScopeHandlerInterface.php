<?php

namespace Sherlockode\AdvancedContentBundle\Scope;

use Sherlockode\AdvancedContentBundle\Model\ContentInterface;
use Sherlockode\AdvancedContentBundle\Model\PageInterface;

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
}
