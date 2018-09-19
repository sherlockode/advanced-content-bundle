<?php

namespace Sherlockode\AdvancedContentBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

abstract class FieldGroupValue implements FieldGroupValueInterface
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var FieldValueInterface
     */
    protected $parent;

    /**
     * @var FieldValueInterface[]|Collection
     */
    protected $children;

    public function __construct()
    {
        $this->children = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getParent()
    {
        return $this->parent;
    }

    public function setParent(FieldValueInterface $fieldValue)
    {
        $this->parent = $fieldValue;

        return $this;
    }

    public function getChildren()
    {
        return $this->children;
    }

    public function addChild(FieldValueInterface $child)
    {
        $this->children->add($child);
        $child->setGroup($this);

        return $this;
    }

    public function removeChild(FieldValueInterface $child)
    {
        $this->children->removeElement($child);

        return $this;
    }
}
