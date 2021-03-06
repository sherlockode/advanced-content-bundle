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
     * @var string
     */
    protected $slug;

    /**
     * @var Collection
     */
    protected $fields;

    /**
     * @var PageTypeInterface
     */
    protected $pageType;

    /**
     * @var PageInterface
     */
    protected $page;

    /**
     * @var Collection|ContentInterface[]
     */
    protected $contents;

    /**
     * @var bool
     */
    protected $allowSeveralContents;

    /**
     * ContentType constructor.
     */
    public function __construct()
    {
        $this->fields = new ArrayCollection();
        $this->contents = new ArrayCollection();
        $this->allowSeveralContents = true;
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
        $field->setContentType(null);

        return $this;
    }

    /**
     * @return PageTypeInterface|null
     */
    public function getPageType()
    {
        return $this->pageType;
    }

    /**
     * @param PageTypeInterface|null $pageType
     *
     * @return $this
     */
    public function setPageType(PageTypeInterface $pageType = null)
    {
        $this->pageType = $pageType;

        return $this;
    }

    /**
     * @return PageInterface
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * @param PageInterface|null $page
     *
     * @return $this
     */
    public function setPage(PageInterface $page = null)
    {
        $this->page = $page;

        return $this;
    }

    /**
     * @return Collection|ContentInterface[]
     */
    public function getContents()
    {
        return $this->contents;
    }

    /**
     * @return ContentInterface|null
     */
    public function getFirstContent()
    {
        return count($this->contents) ? $this->contents[0] : null;
    }

    /**
     * @return bool
     */
    public function isAllowSeveralContents()
    {
        return $this->allowSeveralContents;
    }

    /**
     * @param bool $allowSeveralContents
     *
     * @return $this
     */
    public function setAllowSeveralContents($allowSeveralContents)
    {
        $this->allowSeveralContents = $allowSeveralContents;

        return $this;
    }
}
