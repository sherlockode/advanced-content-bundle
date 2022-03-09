<?php

namespace Sherlockode\AdvancedContentBundle\Locale;

interface TranslatableInterface
{
    /**
     * @return string
     */
    public function getLocale();

    /**
     * @param string $locale
     *
     * @return $this
     */
    public function setLocale($locale);
}
