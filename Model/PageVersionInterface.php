<?php

namespace Sherlockode\AdvancedContentBundle\Model;

interface PageVersionInterface
{
    /**
     * Get page version id
     *
     * @return int
     */
    public function getId();

    /**
     * @return PageInterface
     */
    public function getPage();

    /**
     * @param PageInterface $page
     *
     * @return $this
     */
    public function setPage(PageInterface $page);

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
     * @return ContentVersionInterface
     */
    public function getContentVersion();

    /**
     * @param ContentVersionInterface $contentVersion
     *
     * @return $this
     */
    public function setContentVersion(ContentVersionInterface $contentVersion);

    /**
     * @return PageMetaVersionInterface
     */
    public function getPageMetaVersion();

    /**
     * @param PageMetaVersionInterface $pageMetaVersion
     *
     * @return $this
     */
    public function setPageMetaVersion(PageMetaVersionInterface $pageMetaVersion);

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
