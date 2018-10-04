<?php

namespace Sherlockode\AdvancedContentBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

abstract class Layout implements LayoutInterface
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var FieldInterface
     */
    protected $parent;

    /**
     * @var FieldInterface[]|Collection
     */
    protected $children;

    public function __construct()
    {
        $this->children = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return FieldInterface
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @param FieldInterface $parent
     *
     * @return $this
     */
    public function setParent(FieldInterface $parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * @return Collection|FieldInterface[]
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * @param FieldInterface $child
     *
     * @return $this
     */
    public function addChild(FieldInterface $child)
    {
        $this->children->add($child);
        $child->setLayout($this);

        return $this;
    }

    /**
     * @param FieldInterface $child
     *
     * @return $this
     */
    public function removeChild(FieldInterface $child)
    {
        $this->children->removeElement($child);

        return $this;
    }
}
