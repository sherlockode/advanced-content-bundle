<?php

declare(strict_types=1);

namespace Sherlockode\AdvancedContentBundle\Model;

interface VersionInterface
{
    /**
     * Get version id.
     *
     * @return int
     */
    public function getId();

    /**
     * @return int|null
     */
    public function getUserId();

    /**
     * @return $this
     */
    public function setUserId(?int $userId);

    /**
     * @return \DateTimeInterface
     */
    public function getCreatedAt();

    /**
     * @return $this
     */
    public function setCreatedAt(\DateTimeInterface $createdAt);

    public function isAutoSave(): bool;

    /**
     * @return $this
     */
    public function setAutoSave(bool $autoSave);
}
