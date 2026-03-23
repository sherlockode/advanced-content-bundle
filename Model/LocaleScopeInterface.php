<?php

declare(strict_types=1);

namespace Sherlockode\AdvancedContentBundle\Model;

interface LocaleScopeInterface extends ScopeInterface
{
    /**
     * @return string
     */
    public function getLocale(): ?string;

    /**
     * @return $this
     */
    public function setLocale(string $locale);
}
