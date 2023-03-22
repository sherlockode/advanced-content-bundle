<?php

namespace Sherlockode\AdvancedContentBundle\Model;

abstract class PageVersion implements PageVersionInterface
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var PageInterface
     */
    protected $page;

    /**
     * @var int|null
     */
    protected $userId;

    /**
     * @var ContentVersionInterface
     */
    protected $contentVersion;

    /**
     * @var PageMetaVersionInterface
     */
    protected $pageMetaVersion;

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
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return PageInterface
     */
    public function getPage(): PageInterface
    {
        return $this->page;
    }

    /**
     * @param PageInterface $page
     *
     * @return $this
     */
    public function setPage(PageInterface $page): self
    {
        $this->page = $page;

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
     * @return ContentVersionInterface|null
     */
    public function getContentVersion(): ?ContentVersionInterface
    {
        return $this->contentVersion;
    }

    /**
     * @param ContentVersionInterface $contentVersion
     *
     * @return $this
     */
    public function setContentVersion(ContentVersionInterface $contentVersion): self
    {
        $this->contentVersion = $contentVersion;

        return $this;
    }

    /**
     * @return PageMetaVersionInterface|null
     */
    public function getPageMetaVersion(): ?PageMetaVersionInterface
    {
        return $this->pageMetaVersion;
    }

    /**
     * @param PageMetaVersionInterface $pageMetaVersion
     *
     * @return $this
     */
    public function setPageMetaVersion(PageMetaVersionInterface $pageMetaVersion): self
    {
        $this->pageMetaVersion = $pageMetaVersion;

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
