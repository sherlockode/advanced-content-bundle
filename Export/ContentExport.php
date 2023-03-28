<?php

namespace Sherlockode\AdvancedContentBundle\Export;

use Sherlockode\AdvancedContentBundle\Model\ContentInterface;

class ContentExport
{
    /**
     * @var ElementExport
     */
    private $elementExport;

    /**
     * @var ScopeExport
     */
    private $scopeExport;

    /**
     * @param ElementExport $elementExport
     * @param ScopeExport   $scopeExport
     */
    public function __construct(ElementExport $elementExport, ScopeExport $scopeExport)
    {
        $this->elementExport = $elementExport;
        $this->scopeExport = $scopeExport;
    }

    /**
     * @param ContentInterface $content
     *
     * @return array
     */
    public function exportData(ContentInterface $content)
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
     *
     * @return array
     */
    public function exportElements($elements)
    {
        if (!is_array($elements)) {
            return [];
        }
        if (count($elements) === 0) {
            return [];
        }

        $data = [];
        foreach ($elements as $element) {
            $data[] = $this->elementExport->getElementExportData($element);
        }

        return $data;
    }
}
