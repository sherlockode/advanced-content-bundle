<?php

namespace Sherlockode\AdvancedContentBundle\Model;

interface VersionInterface
{
    /**
     * Get version id
     *
     * @return int
     */
    public function getId();

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
