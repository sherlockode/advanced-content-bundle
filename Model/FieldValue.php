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
     * @var string
     */
    protected $fieldType;

    /**
     * @var string
     */
    protected $value;

    /**
     * @var int
     */
    protected $position;

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

    public function __clone()
    {
        $this->id = null;

        $groupValues = $this->children;
        $this->children = new ArrayCollection();
        foreach ($groupValues as $groupValue) {
            $this->addChild(clone $groupValue);
        }
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
     * @return string
     */
    public function getFieldType()
    {
        return $this->fieldType;
    }

    /**
     * @param string $fieldType
     *
     * @return $this
     */
    public function setFieldType(string $fieldType)
    {
        $this->fieldType = $fieldType;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        if ($this->value === null) {
            return null;
        }
        return unserialize($this->value);
    }

    /**
     * @param mixed $value
     *
     * @return $this
     */
    public function setValue($value)
    {
        $this->value = serialize($value);

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
     * @return Collection|FieldGroupValueInterface[]
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
        $child->setParent(null);

        return $this;
    }
}
