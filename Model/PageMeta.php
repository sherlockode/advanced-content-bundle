<?php

namespace Sherlockode\AdvancedContentBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

abstract class PageMeta implements PageMetaInterface
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
    protected $metaTitle;

    /**
     * @var string
     */
    protected $metaDescription;

    /**
     * @var PageInterface
     */
    protected $page;

    /**
     * @var Collection|PageMetaVersionInterface[]
     */
    protected $versions;

    public function __construct()
    {
        $this->versions = new ArrayCollection();
    }

    public function __clone()
    {
        $this->id = null;
        $this->versions = new ArrayCollection();
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
    public function getMetaTitle()
    {
        return $this->metaTitle;
    }

    /**
     * @param string $metaTitle
     *
     * @return $this
     */
    public function setMetaTitle($metaTitle)
    {
        $this->metaTitle = $metaTitle;

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
     * @return PageInterface
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
    public function setPage(PageInterface $page)
    {
        $this->page = $page;

        return $this;
    }

    /**
     * @return ArrayCollection|Collection|PageMetaVersionInterface[]
     */
    public function getVersions()
    {
        return $this->versions;
    }

    /**
     * @param PageMetaVersionInterface $pageMetaVersion
     *
     * @return $this
     */
    public function addVersion(PageMetaVersionInterface $pageMetaVersion)
    {
        $pageMetaVersion->setPageMeta($this);
        $this->versions->add($pageMetaVersion);

        return $this;
    }

    /**
     * @param PageMetaVersionInterface $pageMetaVersion
     *
     * @return $this
     */
    public function removeVersion(PageMetaVersionInterface $pageMetaVersion)
    {
        $this->versions->removeElement($pageMetaVersion);

        return $this;
    }
}
