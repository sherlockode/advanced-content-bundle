<?php

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

    /**
     * @return PageInterface
     */
    public function getPage(): PageInterface
    {
        return $this->page;
    }

    /**
     * @param PageInterface $page
     *
     * @return $this
     */
    public function setPage(PageInterface $page): self
    {
        $this->page = $page;

        return $this;
    }

    /**
     * @return ContentVersionInterface|null
     */
    public function getContentVersion(): ?ContentVersionInterface
    {
        return $this->contentVersion;
    }

    /**
     * @param ContentVersionInterface $contentVersion
     *
     * @return $this
     */
    public function setContentVersion(ContentVersionInterface $contentVersion): self
    {
        $this->contentVersion = $contentVersion;

        return $this;
    }

    /**
     * @return PageMetaVersionInterface|null
     */
    public function getPageMetaVersion(): ?PageMetaVersionInterface
    {
        return $this->pageMetaVersion;
    }

    /**
     * @param PageMetaVersionInterface $pageMetaVersion
     *
     * @return $this
     */
    public function setPageMetaVersion(PageMetaVersionInterface $pageMetaVersion): self
    {
        $this->pageMetaVersion = $pageMetaVersion;

        return $this;
    }
}
