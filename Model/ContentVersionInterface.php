<?php

namespace Sherlockode\AdvancedContentBundle\Model;

interface ContentVersionInterface
{
    /**
     * Get content id
     *
     * @return int
     */
    public function getId();

    /**
     * @return ContentInterface
     */
    public function getContent();

    /**
     * @param ContentInterface $content
     *
     * @return $this
     */
    public function setContent(ContentInterface $content);

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
     * @return array
     */
    public function getData();

    /**
     * @param array $data
     *
     * @return $this
     */
    public function setData(array $data);

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
}
