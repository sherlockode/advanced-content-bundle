<?php

namespace Sherlockode\AdvancedContentBundle\Scope;

interface ScopeHandlerInterface
{
    /**
     * @return string|null
     */
    public function getScopeGroupBy(): ?string;
}
