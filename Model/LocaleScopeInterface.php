<?php

namespace Sherlockode\AdvancedContentBundle\Model;

interface LocaleScopeInterface extends ScopeInterface
{
    /**
     * @return string
     */
    public function getLocale(): ?string;

    /**
     * @param string $locale
     *
     * @return $this
     */
    public function setLocale(string $locale);
}
