<?php

namespace Sherlockode\AdvancedContentBundle\Export;

use Sherlockode\AdvancedContentBundle\Model\PageInterface;
use Sherlockode\AdvancedContentBundle\Model\PageTypeInterface;

class PageExport
{
    /**
     * @var ContentExport
     */
    private $contentExport;

    /**
     * @var ScopeExport
     */
    private $scopeExport;

    /**
     * @param ScopeExport $scopeExport
     */
    public function __construct(ScopeExport $scopeExport)
    {
        $this->scopeExport = $scopeExport;
    }

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
        $data = array_merge($data, $this->scopeExport->getEntityScopes($page));
        if ($page->getContent() !== null) {
            $data['content'] = $this->contentExport->exportElements($page->getContent()->getData());
        }

        $pageMeta = $page->getPageMeta();
        if ($pageMeta !== null) {
            $data['meta'] = [
                'title'            => $pageMeta->getTitle(),
                'slug'             => $pageMeta->getSlug(),
                'meta_title'       => $pageMeta->getMetaTitle(),
                'meta_description' => $pageMeta->getMetaDescription(),
            ];
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
