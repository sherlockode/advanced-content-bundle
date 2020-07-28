<?php

namespace Sherlockode\AdvancedContentBundle\Export;

use Sherlockode\AdvancedContentBundle\Model\ContentInterface;
use Sherlockode\AdvancedContentBundle\Model\PageInterface;
use Sherlockode\AdvancedContentBundle\Model\PageTypeInterface;

class PageExport
{
    /**
     * @var ContentExport
     */
    private $contentExport;

    /**
     * @param PageInterface $page
     *
     * @return array
     */
    public function exportData(PageInterface $page)
    {
        $data = [];
        $data['status'] = $page->getStatus();
        if ($page->getPageType() instanceof PageTypeInterface) {
            $data['pageType'] = $page->getPageType()->getName();
        }
        $contentData = [];
        foreach ($page->getContents() as $content) {
            $contentType = $content->getContentType();
            if ($contentType->getPage() instanceof PageInterface && $contentType->getPage()->getId() === $page->getId()) {
                $data['contentType'] = $contentType->getSlug();
            }

            $fieldValues = $content->getFieldValues();
            $contentData[$content->getLocale()] = $this->contentExport->exportFieldValues($fieldValues);
        }
        if (count($contentData) > 0) {
            $data['contents'] = $contentData;
        }

        $metaData = [];
        foreach ($page->getPageMetas() as $pageMeta) {
            $localeMeta = [];
            $localeMeta['title'] = $pageMeta->getTitle();
            $localeMeta['slug'] = $pageMeta->getSlug();
            $localeMeta['meta_title'] = $pageMeta->getMetaTitle();
            $localeMeta['meta_description'] = $pageMeta->getMetaDescription();

            $metaData[$pageMeta->getLocale()] = $localeMeta;
        }
        if (count($metaData) > 0) {
            $data['metas'] = $metaData;
        }

        $data = [
            'pages' => [
                $page->getPageIdentifier() => $data,
            ],
        ];

        return $data;
    }

    /**
     * @param ContentExport $contentExport
     */
    public function setContentExport(ContentExport $contentExport)
    {
        $this->contentExport = $contentExport;
    }
}
