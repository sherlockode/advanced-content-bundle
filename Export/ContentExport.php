<?php

declare(strict_types=1);

namespace Sherlockode\AdvancedContentBundle\Export;

use Sherlockode\AdvancedContentBundle\Model\ContentInterface;

class ContentExport
{
    public function __construct(private readonly ElementExport $elementExport, private readonly ScopeExport $scopeExport)
    {
    }

    /**
     * @return array
     */
    public function exportData(ContentInterface $content): array
    {
        $data = [];
        $data['name'] = $content->getName();
        $data = array_merge($data, $this->scopeExport->getEntityScopes($content));

        $elements = $content->getData() ?? [];
        $data['children'] = $this->exportElements($elements);

        return [
            'contents' => [
                $content->getSlug() => $data,
            ],
        ];
    }

    /**
     * @param array|array[] $elements
     *
     * @return array
     */
    public function exportElements($elements): array
    {
        if (!is_array($elements)) {
            return [];
        }

        if ($elements === []) {
            return [];
        }

        $data = [];
        foreach ($elements as $element) {
            $data[] = $this->elementExport->getElementExportData($element);
        }

        return $data;
    }
}
