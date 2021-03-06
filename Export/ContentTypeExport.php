<?php

namespace Sherlockode\AdvancedContentBundle\Export;

use Sherlockode\AdvancedContentBundle\Model\ContentTypeInterface;
use Sherlockode\AdvancedContentBundle\Model\FieldInterface;
use Sherlockode\AdvancedContentBundle\Model\PageTypeInterface;

class ContentTypeExport
{
    /**
     * @param ContentTypeInterface $contentType
     *
     * @return array
     */
    public function exportData(ContentTypeInterface $contentType)
    {
        $data = [];
        $data['name'] = $contentType->getName();
        if ($contentType->getPageType() instanceof PageTypeInterface) {
            $data['pageType'] = $contentType->getPageType()->getName();
        }

        $fields = $contentType->getFields();
        $data = array_merge($data, $this->exportFields($fields));

        $data = [
            'content_types' => [
                $contentType->getSlug() => $data,
            ],
        ];

        return $data;
    }

    /**
     * @param array|FieldInterface[] $fields
     *
     * @return array
     */
    private function exportFields($fields)
    {
        if (count($fields) === 0) {
            return [];
        }

        $data = ['children' => []];
        foreach ($fields as $field) {
            $data['children'][] = $this->exportField($field);
        }

        return $data;
    }

    /**
     * @param FieldInterface $field
     *
     * @return array
     */
    private function exportField(FieldInterface $field)
    {
        $fieldData = [];
        $fieldData['name'] = $field->getName();
        $fieldData['slug'] = $field->getSlug();
        $fieldData['type'] = $field->getType();
        $fieldData['required'] = (int) $field->isRequired();
        if ($options = $field->getOptions()) {
            $fieldData['options'] = $options;
        }

        $children = $field->getChildren();
        if (count($children) > 0) {
            $fieldData['children'] = [];
            foreach ($children as $childLayout) {
                $childLayoutData = [];
                $childLayoutData['layout_name'] = $childLayout->getName();
                $childLayoutData = array_merge($childLayoutData, $this->exportFields($childLayout->getChildren()));
                $fieldData['children'][] = $childLayoutData;
            }
        }

        return $fieldData;
    }
}
