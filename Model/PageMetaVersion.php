<?php

namespace Sherlockode\AdvancedContentBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

abstract class PageMetaVersion implements PageMetaVersionInterface
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var PageMetaInterface
     */
    protected $pageMeta;

    /**
     * @var int|null
     */
    protected $userId;

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
     * @var \DateTimeInterface
     */
    protected $createdAt;

    /**
     * @var bool
     */
    protected $autoSave;

    public function __construct()
    {
        $this->autoSave = false;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return PageMetaInterface
     */
    public function getPageMeta(): PageMetaInterface
    {
        return $this->pageMeta;
    }

    /**
     * @param PageMetaInterface $pageMeta
     *
     * @return $this
     */
    public function setPageMeta(PageMetaInterface $pageMeta): self
    {
        $this->pageMeta = $pageMeta;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getUserId(): ?int
    {
        return $this->userId;
    }

    /**
     * @param int|null $userId
     *
     * @return $this
     */
    public function setUserId(?int $userId): self
    {
        $this->userId = $userId;

        return $this;
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
     * @return \DateTimeInterface
     */
    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTimeInterface $createdAt
     *
     * @return $this
     */
    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return bool
     */
    public function isAutoSave(): bool
    {
        return $this->autoSave;
    }

    /**
     * @param bool $autoSave
     *
     * @return $this
     */
    public function setAutoSave(bool $autoSave): self
    {
        $this->autoSave = $autoSave;

        return $this;
    }
}
