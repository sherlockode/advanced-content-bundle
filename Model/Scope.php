<?php

declare(strict_types=1);

namespace Sherlockode\AdvancedContentBundle\Model;

abstract class Scope implements ScopeInterface
{
    /**
     * @var int
     */
    protected $id;

    public function getId(): int
    {
        return $this->id;
    }
}
