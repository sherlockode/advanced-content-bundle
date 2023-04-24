<?php

namespace Sherlockode\AdvancedContentBundle\Model;

interface PageMetaVersionInterface
{
    /**
     * Get page meta version id
     *
     * @return int
     */
    public function getId();

    /**
     * @return PageMetaInterface
     */
    public function getPageMeta();

    /**
     * @param PageMetaInterface $pageMeta
     *
     * @return $this
     */
    public function setPageMeta(PageMetaInterface $pageMeta);

    /**
     * @return int|null
     */
    public function getUserId();

    /**
     * @param int|null $userId
     *
     * @return $this
     */
    public function setUserId(?int $userId);

    /**
     * @return string
     */
    public function getTitle();

    /**
     * @param string $title
     *
     * @return $this
     */
    public function setTitle($title);

    /**
     * @return string
     */
    public function getSlug();

    /**
     * @param string $slug
     *
     * @return $this
     */
    public function setSlug($slug);

    /**
     * @return string
     */
    public function getMetaTitle();

    /**
     * @param string $metaTitle
     *
     * @return $this
     */
    public function setMetaTitle($metaTitle);

    /**
     * @return string
     */
    public function getMetaDescription();

    /**
     * @param string $metaDescription
     *
     * @return $this
     */
    public function setMetaDescription($metaDescription);

    /**
     * @return \DateTimeInterface
     */
    public function getCreatedAt();

    /**
     * @param \DateTimeInterface $createdAt
     *
     * @return $this
     */
    public function setCreatedAt(\DateTimeInterface $createdAt);

    /**
     * @return bool
     */
    public function isAutoSave(): bool;

    /**
     * @param bool $autoSave
     *
     * @return $this
     */
    public function setAutoSave(bool $autoSave);
}
