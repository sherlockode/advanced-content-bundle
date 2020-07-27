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
    public function getPageIdentifier();

    /**
     * @param string $pageIdentifier
     *
     * @return $this
     */
    public function setPageIdentifier($pageIdentifier);

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

    /**
     * @return PageMetaInterface[]
     */
    public function getPageMetas();

    /**
     * @param PageMetaInterface $pageMeta
     *
     * @return $this
     */
    public function addPageMeta(PageMetaInterface $pageMeta);

    /**
     * @param PageMetaInterface $pageMeta
     *
     * @return $this
     */
    public function removePageMeta(PageMetaInterface $pageMeta);

    /**
     * Get a linked page meta with the requested locale
     * If none match, the first page meta from the $pageMetas property is returned
     *
     * @param string|null $locale
     *
     * @return PageMetaInterface
     */
    public function getPageMeta($locale = null);

    /**
     * Replace all existing linked page metas with a single one
     * Used in form when only one page meta is expected (multilang disabled)
     *
     * @param PageMetaInterface|null $pageMeta
     *
     * @return $this
     */
    public function setPageMeta(PageMetaInterface $pageMeta = null);
}
