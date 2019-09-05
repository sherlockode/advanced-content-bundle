<?php

namespace Sherlockode\AdvancedContentBundle\Model;

interface PageTypeInterface
{
    /**
     * Get field id
     *
     * @return int
     */
    public function getId();

    /**
     * Get page type's name
     *
     * @return string
     */
    public function getName();

    /**
     * Set page type's name
     *
     * @param string $name
     *
     * @return $this
     */
    public function setName($name);
}
