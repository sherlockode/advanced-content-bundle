<?php

namespace Sherlockode\AdvancedContentBundle\Locale;

interface LocaleProviderInterface
{
    /**
     * @return bool
     */
    public function isMultilangEnabled();

    /**
     * @return array
     */
    public function getLocales();

    /**
     * @return string
     */
    public function getDefaultLocale();
}
