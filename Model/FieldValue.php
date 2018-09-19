<?php

namespace Sherlockode\AdvancedContentBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

abstract class FieldValue implements FieldValueInterface
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var ContentInterface
     */
    protected $content;

    /**
     * @var FieldInterface
     */
    protected $field;

    /**
     * @var string
     */
    protected $value;

    /**
     * @var FieldGroupValueInterface
     */
    protected $group;

    /**
     * @var FieldGroupValueInterface[]|Collection
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
     * @return ContentInterface
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param ContentInterface $content
     *
     * @return $this
     */
    public function setContent(ContentInterface $content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * @return FieldInterface
     */
    public function getField()
    {
        return $this->field;
    }

    /**
     * @param FieldInterface $field
     *
     * @return $this
     */
    public function setField(FieldInterface $field)
    {
        $this->field = $field;

        return $this;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * @return FieldGroupValueInterface
     */
    public function getGroup()
    {
        return $this->group;
    }

    /**
     * @param FieldGroupValueInterface|null $group
     *
     * @return $this
     */
    public function setGroup(FieldGroupValueInterface $group = null)
    {
        $this->group = $group;

        return $this;
    }

    /**
     * @return ArrayCollection|Collection|FieldGroupValueInterface[]
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * @param FieldGroupValueInterface $child
     *
     * @return $this
     */
    public function addChild(FieldGroupValueInterface $child)
    {
        $this->children->add($child);
        $child->setParent($this);

        return $this;
    }

    /**
     * @param FieldGroupValueInterface $child
     *
     * @return $this
     */
    public function removeChild(FieldGroupValueInterface $child)
    {
        $this->children->removeElement($child);

        return $this;
    }
}
