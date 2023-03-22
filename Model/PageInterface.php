<?php

namespace Sherlockode\AdvancedContentBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

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
     * @return ContentInterface|null
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

    /**
     * @return PageMetaInterface
     */
    public function getPageMeta();

    /**
     * @param PageMetaInterface $pageMeta
     *
     * @return $this
     */
    public function setPageMeta(PageMetaInterface $pageMeta);

    /**
     * @return PageVersionInterface|null
     */
    public function getPageVersion(): ?PageVersionInterface;

    /**
     * @param PageVersionInterface|null $pageVersion
     *
     * @return $this
     */
    public function setPageVersion(?PageVersionInterface $pageVersion);

    /**
     * @return ArrayCollection|Collection|PageVersionInterface[]
     */
    public function getVersions();
}
