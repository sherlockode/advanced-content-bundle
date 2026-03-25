<?php

namespace Sherlockode\AdvancedContentBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

interface ScopableInterface
{
    /**
     * @return ArrayCollection|Collection|ScopeInterface[]
     */
    public function getScopes();

    /**
     * @return $this
     */
    public function addScope(ScopeInterface $scope);

    /**
     * @return $this
     */
    public function removeScope(ScopeInterface $scope);
}
