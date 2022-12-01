<?php

namespace Sherlockode\AdvancedContentBundle\Export;

use Sherlockode\AdvancedContentBundle\FieldType\Content;
use Sherlockode\AdvancedContentBundle\FieldType\File;
use Sherlockode\AdvancedContentBundle\Manager\FieldManager;
use Sherlockode\AdvancedContentBundle\Model\ContentInterface;
use Sherlockode\AdvancedContentBundle\Model\FieldValueInterface;

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

        $fieldValues = $content->getFieldValues();
        $data['children'] = $this->exportFieldValues($fieldValues);

        $data = [
            'contents' => [
                $content->getSlug() => $data,
            ],
        ];

        return $data;
    }

    /**
     * @param array|FieldValueInterface[] $fieldValues
     *
     * @return array
     */
    public function exportFieldValues($fieldValues)
    {
        if (count($fieldValues) === 0) {
            return [];
        }

        $data = [];
        foreach ($fieldValues as $fieldValue) {
            $data[] = $this->exportFieldValue($fieldValue);
        }

        return $data;
    }

    /**
     * @param FieldValueInterface $fieldValue
     *
     * @return array
     */
    private function exportFieldValue(FieldValueInterface $fieldValue)
    {
        $fieldValueData = [];
        $fieldValueData['type'] = $fieldValue->getFieldType();

        $fieldType = $this->fieldManager->getFieldTypeByCode($fieldValue->getFieldType());
        $rawValue = $fieldType->getRawValue($fieldValue);

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

        $fieldValueData['value'] = $rawValue;

        return $fieldValueData;
    }
}
