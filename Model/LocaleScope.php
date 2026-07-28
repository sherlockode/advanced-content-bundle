<?php

declare(strict_types=1);

namespace Sherlockode\AdvancedContentBundle\Model;

class LocaleScope extends Scope implements LocaleScopeInterface
{
    /**
     * @var string
     */
    protected $locale;

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

    public function getOptionTitle(): string
    {
        return (string) $this->locale;
    }

    public function getUnicityIdentifier(): string
    {
        return (string) $this->locale;
    }
}
