<?php

namespace Sherlockode\AdvancedContentBundle\Model;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

abstract class Content implements ContentInterface
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
     * @var ContentTypeInterface
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
     * @return Collection|FieldValueInterface[]
     */
    public function getFieldValues()
    {
        return $this->fieldValues;
    }

    /**
     * @param FieldValueInterface $fieldValue
     *
     * @return $this
     */
    public function addFieldValue(FieldValueInterface $fieldValue)
    {
        $this->fieldValues[] = $fieldValue;
        $fieldValue->setContent($this);

        return $this;
    }

    /**
     * @param FieldValueInterface $fieldValue
     *
     * @return $this
     */
    public function removeFieldValue(FieldValueInterface $fieldValue)
    {
        $this->fieldValues->removeElement($fieldValue);

        return $this;
    }
}
