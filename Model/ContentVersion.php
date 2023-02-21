<?php

namespace Sherlockode\AdvancedContentBundle\Model;

abstract class ContentVersion implements ContentVersionInterface
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var ContentInterface
     */
    protected $content;

    /**
     * @var int|null
     */
    protected $userId;

    /**
     * @var array
     */
    protected $data;

    /**
     * @var \DateTimeInterface
     */
    protected $createdAt;

    /**
     * ContentVersion constructor
     */
    public function __construct()
    {
        $this->data = [];
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return ContentInterface
     */
    public function getContent(): ContentInterface
    {
        return $this->content;
    }

    /**
     * @param ContentInterface $content
     *
     * @return $this
     */
    public function setContent(ContentInterface $content): self
    {
        $this->content = $content;

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
     * @return array
     */
    public function getData()
    {
        $data = $this->data ?? [];
        uasort($data, function ($a, $b) {
            return ($a['position'] ?? 0) <=> ($b['position'] ?? 0);
        });

        return $data;
    }

    /**
     * @param array $data
     *
     * @return $this
     */
    public function setData(array $data)
    {
        $this->data = $data;

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
}
