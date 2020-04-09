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
     * @var string
     */
    protected $slug;

    /**
     * @var ContentTypeInterface
     */
    protected $contentType;

    /**
     * @var Collection
     */
    protected $fieldValues;

    /**
     * @var PageInterface
     */
    protected $page;

    /**
     * @var string
     */
    protected $locale;

    /**
     * Content constructor
     */
    public function __construct()
    {
        $this->fieldValues = new ArrayCollection();
    }

    public function __clone()
    {
        $this->id = null;
        $fieldValues = $this->fieldValues;
        $this->fieldValues = new ArrayCollection();
        foreach ($fieldValues as $fieldValue) {
            $this->addFieldValue(clone $fieldValue);
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

    /**
     * @return PageInterface|null
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * @param PageInterface $page
     *
     * @return $this
     */
    public function setPage(PageInterface $page = null)
    {
        $this->page = $page;

        return $this;
    }

    /**
     * @return string
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * @param string $locale
     *
     * @return $this
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;

        return $this;
    }
}
