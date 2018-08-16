<?php

namespace Sherlockode\AdvancedContentBundle\Model;

use Gedmo\Mapping\Annotation as Gedmo;

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
     *
     * @Gedmo\Slug(fields={"name"})
     */
    protected $slug;

    /**
     * @var bool
     */
    protected $required;

    /**
     * @var array
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
        return $this->options;
    }

    /**
     * @param array $options
     *
     * @return $this
     */
    public function setOptions($options)
    {
        $this->options = $options;

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
}
