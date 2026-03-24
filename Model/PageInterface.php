<?php

namespace Sherlockode\AdvancedContentBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

interface PageInterface
{
    public const STATUS_DRAFT = 0;

    public const STATUS_PUBLISHED = 10;

    public const STATUS_TRASH = 20;

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
     * @return $this
     */
    public function setContent(?ContentInterface $content = null);

    /**
     * @return PageTypeInterface|null
     */
    public function getPageType();

    /**
     * @return $this
     */
    public function setPageType(?PageTypeInterface $pageType = null);

    /**
     * @return PageMetaInterface
     */
    public function getPageMeta();

    /**
     * @return $this
     */
    public function setPageMeta(PageMetaInterface $pageMeta);

    public function getPageVersion(): ?PageVersionInterface;

    /**
     * @return $this
     */
    public function setPageVersion(?PageVersionInterface $pageVersion);

    /**
     * @return ArrayCollection|Collection|PageVersionInterface[]
     */
    public function getVersions();
}
