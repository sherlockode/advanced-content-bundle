<?php

namespace Sherlockode\AdvancedContentBundle\Model;

abstract class Version implements VersionInterface
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var int|null
     */
    protected $userId;

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
