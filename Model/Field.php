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
    protected $position;

    /**
     * @var string
     */
    protected $hint;

    /**
     * @var LayoutInterface[]|Collection
     */
    protected $children;

    /**
     * @var LayoutInterface
     */
    protected $layout;

    public function __construct()
    {
        $this->required = false;
        $this->position = 0;
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
     * @param ContentTypeInterface|null $contentType
     *
     * @return $this
     */
    public function setContentType(ContentTypeInterface $contentType = null)
    {
        $this->contentType = $contentType;

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
     * @return Collection|LayoutInterface[]
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * @param LayoutInterface $child
     *
     * @return $this
     */
    public function addChild(LayoutInterface $child)
    {
        $this->children->add($child);
        $child->setParent($this);

        return $this;
    }

    /**
     * @param LayoutInterface $child
     *
     * @return $this
     */
    public function removeChild(LayoutInterface $child)
    {
        $this->children->removeElement($child);
        $child->setParent(null);

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
}
