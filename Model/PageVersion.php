<?php

declare(strict_types=1);

namespace Sherlockode\AdvancedContentBundle\Model;

abstract class PageVersion extends Version implements PageVersionInterface
{
    /**
     * @var PageInterface
     */
    protected $page;

    /**
     * @var ContentVersionInterface
     */
    protected $contentVersion;

    /**
     * @var PageMetaVersionInterface
     */
    protected $pageMetaVersion;

    public function getPage(): PageInterface
    {
        return $this->page;
    }

    /**
     * @return $this
     */
    public function setPage(PageInterface $page): self
    {
        $this->page = $page;

        return $this;
    }

    public function getContentVersion(): ?ContentVersionInterface
    {
        return $this->contentVersion;
    }

    /**
     * @return $this
     */
    public function setContentVersion(ContentVersionInterface $contentVersion): self
    {
        $this->contentVersion = $contentVersion;

        return $this;
    }

    public function getPageMetaVersion(): ?PageMetaVersionInterface
    {
        return $this->pageMetaVersion;
    }

    /**
     * @return $this
     */
    public function setPageMetaVersion(PageMetaVersionInterface $pageMetaVersion): self
    {
        $this->pageMetaVersion = $pageMetaVersion;

        return $this;
    }
}
