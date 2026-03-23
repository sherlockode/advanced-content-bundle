<?php

declare(strict_types=1);

namespace Sherlockode\AdvancedContentBundle\Export;

use Sherlockode\AdvancedContentBundle\Model\PageInterface;
use Sherlockode\AdvancedContentBundle\Model\PageTypeInterface;

class PageExport
{
    private ?ContentExport $contentExport = null;

    public function __construct(private readonly ScopeExport $scopeExport)
    {
    }

    /**
     * @return array
     */
    public function exportData(PageInterface $page): array
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

        return [
            'pages' => [
                $page->getPageIdentifier() => $data,
            ],
        ];
    }

    public function setContentExport(ContentExport $contentExport): void
    {
        $this->contentExport = $contentExport;
    }
}
