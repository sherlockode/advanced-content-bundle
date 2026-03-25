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

    public function getId(): int
    {
        return $this->id;
    }

    public function getUserId(): ?int
    {
        return $this->userId;
    }

    /**
     * @return $this
     */
    public function setUserId(?int $userId): self
    {
        $this->userId = $userId;

        return $this;
    }

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    /**
     * @return $this
     */
    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function isAutoSave(): bool
    {
        return $this->autoSave;
    }

    /**
     * @return $this
     */
    public function setAutoSave(bool $autoSave): self
    {
        $this->autoSave = $autoSave;

        return $this;
    }
}
