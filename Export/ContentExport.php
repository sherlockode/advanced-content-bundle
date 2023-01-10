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
     * @param ElementExport $elementExport
     */
    public function __construct(ElementExport $elementExport)
    {
        $this->elementExport = $elementExport;
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
        if ($content->getLocale()) {
            $data['locale'] = $content->getLocale();
        }

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
