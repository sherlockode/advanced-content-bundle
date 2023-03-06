<?php

namespace Sherlockode\AdvancedContentBundle\Model;

class LocaleScope extends Scope implements LocaleScopeInterface
{
    /**
     * @var string
     */
    protected $locale;

    /**
     * @return string
     */
    public function getLocale(): ?string
    {
        return $this->locale;
    }

    /**
     * @param string $locale
     *
     * @return $this
     */
    public function setLocale($locale): self
    {
        $this->locale = $locale;

        return $this;
    }

    /**
     * @return string
     */
    public function getOptionTitle()
    {
        return (string)$this->locale;
    }

    /**
     * @return string
     */
    public function getUnicityIdentifier()
    {
        return (string)$this->locale;
    }
}
