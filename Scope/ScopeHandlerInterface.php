<?php

namespace Sherlockode\AdvancedContentBundle\Scope;

use Sherlockode\AdvancedContentBundle\Model\ContentInterface;

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
}
