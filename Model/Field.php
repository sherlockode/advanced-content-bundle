<?php

namespace Sherlockode\AdvancedContentBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

abstract class Field implements FieldInterface
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $slug;

    /**
     * @var bool
     */
    protected $required;

    /**
     * @var string
     */
    protected $options;

    /**
     * @var ContentTypeInterface
     */
    protected $contentType;

    /**
     * @var int
     */
    protected $sortOrder;

    /**
     * @var string
     */
    protected $hint;

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
        $this->required = false;
        $this->sortOrder = 0;
        $this->options = serialize([]);
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
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     *
     * @return $this
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
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
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * @param string $slug
     *
     * @return $this
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * @return bool
     */
    public function isRequired()
    {
        return $this->required;
    }

    /**
     * @param bool $required
     *
     * @return $this
     */
    public function setRequired($required)
    {
        $this->required = $required;

        return $this;
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return unserialize($this->options);
    }

    /**
     * @param array $options
     *
     * @return $this
     */
    public function setOptions($options)
    {
        $this->options = serialize($options);

        return $this;
    }

    /**
     * @return ContentTypeInterface
     */
    public function getContentType()
    {
        return $this->contentType;
    }

    /**
     * @param ContentTypeInterface $contentType
     *
     * @return $this
     */
    public function setContentType(ContentTypeInterface $contentType)
    {
        $this->contentType = $contentType;

        return $this;
    }

    /**
     * @return int
     */
    public function getSortOrder()
    {
        return $this->sortOrder;
    }

    /**
     * @param int $sortOrder
     *
     * @return $this
     */
    public function setSortOrder($sortOrder)
    {
        $this->sortOrder = $sortOrder;

        return $this;
    }

    /**
     * Get field's hint
     *
     * @return string
     */
    public function getHint()
    {
        return $this->hint;
    }

    /**
     * Set field's hint
     *
     * @param string $hint
     *
     * @return $this
     */
    public function setHint($hint)
    {
        $this->hint = $hint;

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
     * @param FieldInterface|null $parent
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
        $child->setParent($this);

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
