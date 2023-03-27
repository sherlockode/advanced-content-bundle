<?php

namespace Sherlockode\AdvancedContentBundle\Model;

interface ScopeInterface
{
    /**
     * @return int|null
     */
    public function getId();

    /**
     * @return string
     */
    public function getOptionTitle();

    /**
     * @return string
     */
    public function getUnicityIdentifier();
}
