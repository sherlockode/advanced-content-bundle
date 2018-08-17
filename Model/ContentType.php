<?php

namespace Sherlockode\AdvancedContentBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

abstract class ContentType implements ContentTypeInterface
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
     * @var Collection
     */
    protected $fields;

    /**
     * ContentType constructor.
     */
    public function __construct()
    {
        $this->fields = new ArrayCollection();
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
     * @return Collection|FieldInterface[]
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * @param FieldInterface $field
     *
     * @return $this
     */
    public function addField(FieldInterface $field)
    {
        $this->fields[] = $field;
        $field->setContentType($this);

        return $this;
    }

    /**
     * @param FieldInterface $field
     *
     * @return $this
     */
    public function removeField(FieldInterface $field)
    {
        $this->fields->removeElement($field);

        return $this;
    }
}
