<?php

namespace Sherlockode\AdvancedContentBundle\Model;

interface PageInterface
{
    const STATUS_DRAFT  = 0;
    const STATUS_PUBLISHED = 10;
    const STATUS_TRASH  = 20;

    /**
     * @return int
     */
    public function getId();

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
    public function getMetaDescription();

    /**
     * @param string $metaDescription
     *
     * @return $this
     */
    public function setMetaDescription($metaDescription);

    /**
     * @return int
     */
    public function getStatus();

    /**
     * @param int $status
     *
     * @return $this
     */
    public function setStatus($status);

    /**
     * @return ContentInterface
     */
    public function getContent();

    /**
     * @param ContentInterface|null $content
     *
     * @return $this
     */
    public function setContent(ContentInterface $content = null);

    /**
     * @return PageTypeInterface|null
     */
    public function getPageType();

    /**
     * @param PageTypeInterface|null $pageType
     *
     * @return $this
     */
    public function setPageType(PageTypeInterface $pageType = null);
}
