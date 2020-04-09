<?php

namespace Sherlockode\AdvancedContentBundle\Locale;

class LocaleProvider implements LocaleProviderInterface
{
    /**
     * @var array
     */
    private $locales;

    /**
     * @param array $locales
     */
    public function __construct(array $locales)
    {
        $this->locales = $locales;
    }

    /**
     * @return bool
     */
    public function isMultilangEnabled()
    {
        return count($this->locales) > 1;
    }

    /**
     * @return array
     */
    public function getLocales()
    {
        return $this->locales;
    }

    /**
     * @return string
     */
    public function getDefaultLocale()
    {
        return reset($this->locales);
    }
}
