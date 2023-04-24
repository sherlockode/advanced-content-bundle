<?php

namespace Sherlockode\AdvancedContentBundle\Model;

interface PageVersionInterface
{
    /**
     * @return PageInterface
     */
    public function getPage();

    /**
     * @param PageInterface $page
     *
     * @return $this
     */
    public function setPage(PageInterface $page);

    /**
     * @return ContentVersionInterface
     */
    public function getContentVersion();

    /**
     * @param ContentVersionInterface $contentVersion
     *
     * @return $this
     */
    public function setContentVersion(ContentVersionInterface $contentVersion);

    /**
     * @return PageMetaVersionInterface
     */
    public function getPageMetaVersion();

    /**
     * @param PageMetaVersionInterface $pageMetaVersion
     *
     * @return $this
     */
    public function setPageMetaVersion(PageMetaVersionInterface $pageMetaVersion);
}
