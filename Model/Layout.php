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

    /**
     * @var FieldGroupValueInterface[]|Collection
     */
    protected $fieldGroupValues;

    /**
     * @var int
     */
    protected $position;

    public function __construct()
    {
        $this->children = new ArrayCollection();
        $this->fieldGroupValues = new ArrayCollection();
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

    /**
     * @return Collection|FieldGroupValueInterface[]
     */
    public function getFieldGroupValues()
    {
        return $this->fieldGroupValues;
    }

    /**
     * @param FieldGroupValueInterface $fieldGroupValue
     *
     * @return $this
     */
    public function addFieldGroupValue(FieldGroupValueInterface $fieldGroupValue)
    {
        $this->fieldGroupValues->add($fieldGroupValue);
        $fieldGroupValue->setLayout($this);

        return $this;
    }

    /**
     * @param FieldGroupValueInterface $fieldGroupValue
     *
     * @return $this
     */
    public function removeFieldGroupValue(FieldGroupValueInterface $fieldGroupValue)
    {
        $this->fieldGroupValues->removeElement($fieldGroupValue);
        $fieldGroupValue->setLayout(null);

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
