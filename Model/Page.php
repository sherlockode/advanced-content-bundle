<?php

namespace Sherlockode\AdvancedContentBundle\Model;

abstract class Page implements PageInterface
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $title;

    /**
     * @var string
     */
    protected $slug;

    /**
     * @var string
     */
    protected $metaDescription;

    /**
     * @var integer
     */
    protected $status;

    /**
     * @var ContentInterface
     */
    protected $content;

    /**
     * @var PageTypeInterface
     */
    protected $pageType;

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
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     *
     * @return $this
     */
    public function setTitle($title)
    {
        $this->title = $title;

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
     * @return string
     */
    public function getMetaDescription()
    {
        return $this->metaDescription;
    }

    /**
     * @param string $metaDescription
     *
     * @return $this
     */
    public function setMetaDescription($metaDescription)
    {
        $this->metaDescription = $metaDescription;

        return $this;
    }

    /**
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param int $status
     *
     * @return $this
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return ContentInterface|null
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param ContentInterface|null $content
     *
     * @return $this
     */
    public function setContent(ContentInterface $content = null)
    {
        $this->content = $content;

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
}
