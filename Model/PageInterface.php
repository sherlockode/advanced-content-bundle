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
     * Get a linked content with the requested locale
     * If none match, the first content from the $contents property is returned
     *
     * @param string|null $locale
     *
     * @return ContentInterface
     */
    public function getContent($locale = null);

    /**
     * Replace all existing linked contents with a single one
     * Used in form when only one content is expected (multilang disabled)
     *
     * @param ContentInterface|null $content
     *
     * @return $this
     */
    public function setContent(ContentInterface $content = null);

    /**
     * @return ContentInterface[]
     */
    public function getContents();

    /**
     * @param ContentInterface $content
     *
     * @return $this
     */
    public function addContent(ContentInterface $content);

    /**
     * @param ContentInterface $content
     *
     * @return $this
     */
    public function removeContent(ContentInterface $content);

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

    /**
     * @param string $locale
     *
     * @return $this
     */
    public function setCurrentLocale($locale);
}
