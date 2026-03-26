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

    public function __construct(private readonly ScopeExport $scopeExport)
    {
    }

    /**
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
        if (null !== $page->getContent()) {
            $data['content'] = $this->contentExport->exportElements($page->getContent()->getData());
        }

        $pageMeta = $page->getPageMeta();
        if (null !== $pageMeta) {
            $data['meta'] = [
                'title' => $pageMeta->getTitle(),
                'slug' => $pageMeta->getSlug(),
                'meta_title' => $pageMeta->getMetaTitle(),
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

    public function setContentExport(ContentExport $contentExport)
    {
        $this->contentExport = $contentExport;
    }
}
