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

    /**
     * @var LayoutInterface
     */
    protected $layout;

    /**
     * @var int
     */
    protected $position;

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

    public function setParent(FieldValueInterface $fieldValue = null)
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

    /**
     * @return LayoutInterface
     */
    public function getLayout()
    {
        return $this->layout;
    }

    /**
     * @param LayoutInterface $layout
     *
     * @return $this
     */
    public function setLayout(LayoutInterface $layout = null)
    {
        $this->layout = $layout;

        return $this;
    }

    /**
     * @return int
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * @param int $position
     *
     * @return $this
     */
    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
    }
}
