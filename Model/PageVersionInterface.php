<?php

namespace Sherlockode\AdvancedContentBundle\Model;

interface PageVersionInterface
{
    /**
     * @return PageInterface
     */
    public function getPage();

    /**
     * @return $this
     */
    public function setPage(PageInterface $page);

    /**
     * @return ContentVersionInterface
     */
    public function getContentVersion();

    /**
     * @return $this
     */
    public function setContentVersion(ContentVersionInterface $contentVersion);

    /**
     * @return PageMetaVersionInterface
     */
    public function getPageMetaVersion();

    /**
     * @return $this
     */
    public function setPageMetaVersion(PageMetaVersionInterface $pageMetaVersion);
}
