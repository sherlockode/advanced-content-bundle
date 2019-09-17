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
        $data['title'] = $page->getTitle();
        $data['meta'] = $page->getMetaDescription();
        $data['status'] = $page->getStatus();
        if ($page->getPageType() instanceof PageTypeInterface) {
            $data['pageType'] = $page->getPageType()->getName();
        }
        if ($page->getContent() instanceof ContentInterface) {
            $content = $page->getContent();
            $contentType = $content->getContentType();
            if ($contentType->getPage() instanceof PageInterface && $contentType->getPage()->getId() === $page->getId()) {
                $data['contentType'] = $contentType->getSlug();
            }

            $fieldValues = $content->getFieldValues();
            $data = array_merge($data, $this->contentExport->exportFieldValues($fieldValues));
        }

        $data = [
            'pages' => [
                $page->getSlug() => $data,
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
