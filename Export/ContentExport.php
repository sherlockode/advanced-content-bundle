<?php

namespace Sherlockode\AdvancedContentBundle\Export;

use Sherlockode\AdvancedContentBundle\FieldType\Content;
use Sherlockode\AdvancedContentBundle\FieldType\File;
use Sherlockode\AdvancedContentBundle\Manager\FieldManager;
use Sherlockode\AdvancedContentBundle\Model\ContentInterface;

class ContentExport
{
    /**
     * @var FieldManager
     */
    private $fieldManager;

    /**
     * @param FieldManager $fieldManager
     */
    public function __construct(FieldManager $fieldManager)
    {
        $this->fieldManager = $fieldManager;
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
            $data[] = $this->exportElement($element);
        }

        return $data;
    }

    /**
     * @param array $element
     *
     * @return array
     */
    private function exportElement(array $element)
    {
        $elementData = [];
        $elementData['type'] = $element['fieldType'];

        $fieldType = $this->fieldManager->getFieldTypeByCode($element['fieldType']);
        $rawValue = $fieldType->getRawValue($element['value'] ?? null);

        if ($fieldType instanceof File) {
            if (is_array($rawValue) && isset($rawValue['url'])) {
                unset($rawValue['url']);
            }
        }
        if ($fieldType instanceof Content) {
            if (is_array($rawValue) && isset($rawValue['entity'])) {
                $rawValue['_content'] = [
                    'slug' => $rawValue['entity']->getSlug(),
                    'locale' => $rawValue['entity']->getLocale(),
                ];
                unset($rawValue['entity']);
                unset($rawValue['content']);
            }
        }

        $elementData['value'] = $rawValue;

        return $elementData;
    }
}
