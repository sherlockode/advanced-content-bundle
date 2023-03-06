<?php

namespace Sherlockode\AdvancedContentBundle\Model;

abstract class Scope implements ScopeInterface
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }
}
