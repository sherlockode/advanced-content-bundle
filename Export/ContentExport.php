<?php

declare(strict_types=1);

namespace Sherlockode\AdvancedContentBundle\Export;

use Sherlockode\AdvancedContentBundle\Model\ContentInterface;

class ContentExport
{
    public function __construct(
        private readonly ElementExport $elementExport,
        private readonly ScopeExport $scopeExport,
    ) {
    }

    public function exportData(ContentInterface $content): array
    {
        $data = [];
        $data['name'] = $content->getName();
        $data = array_merge($data, $this->scopeExport->getEntityScopes($content));

        $elements = $content->getData() ?? [];
        $data['children'] = $this->exportElements($elements);

        $data = [
            'contents' => [
                $content->getSlug() => $data,
            ],
        ];

        return $data;
    }

    /**
     * @param array|array[] $elements
     */
    public function exportElements($elements): array
    {
        if (!is_array($elements)) {
            return [];
        }

        if ([] === $elements) {
            return [];
        }

        $data = [];
        foreach ($elements as $element) {
            $data[] = $this->elementExport->getElementExportData($element);
        }

        return $data;
    }
}
