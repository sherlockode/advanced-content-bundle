<?php

namespace Sherlockode\AdvancedContentBundle\Model;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

abstract class Content
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var ContentType
     */
    protected $contentType;

    /**
     * @var Collection
     */
    protected $fieldValues;

    /**
     * Content constructor
     */
    public function __construct()
    {
        $this->fieldValues = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return ContentType
     */
    public function getContentType()
    {
        return $this->contentType;
    }

    /**
     * @param ContentType $contentType
     *
     * @return $this
     */
    public function setContentType(ContentType $contentType)
    {
        $this->contentType = $contentType;

        return $this;
    }

    /**
     * @return Collection|FieldValue[]
     */
    public function getFieldValues()
    {
        return $this->fieldValues;
    }

    /**
     * @param FieldValue $fieldValue
     *
     * @return $this
     */
    public function addFieldValue(FieldValue $fieldValue)
    {
        $this->fieldValues[] = $fieldValue;

        return $this;
    }

    /**
     * @param FieldValue $fieldValue
     *
     * @return $this
     */
    public function removeFieldValue(FieldValue $fieldValue)
    {
        $this->fieldValues->removeElement($fieldValue);

        return $this;
    }
}
