<?php

namespace Sherlockode\AdvancedContentBundle\FieldType;

use Ivory\CKEditorBundle\Form\Type\CKEditorType;
use Sherlockode\AdvancedContentBundle\Model\FieldInterface;

class Wysiwyg extends AbstractFieldType
{
    /**
     * Get available options for given field type
     *
     * @return array
     */
    public function getFieldTypeOptions()
    {
        return [
            'toolbar' => [
                'label' => 'Toolbar',
                'type'  => 'choices'
            ],
        ];
    }

    /**
     * Get options to apply on field value
     *
     * @param FieldInterface $field
     *
     * @return array
     */
    public function getFormFieldValueOptions(FieldInterface $field)
    {
        $fieldOptions = $this->getFieldOptions($field);

        if (!isset($fieldOptions['toolbar'])) {
            throw new \RuntimeException("Missing mandatory option toolbar.");
        }

        $formFieldOptions = ['config' => ['toolbar' => $fieldOptions['toolbar']]];

        return $formFieldOptions;
    }

    /**
     * @return string
     */
    public function getFormFieldType()
    {
        return CKEditorType::class;
    }
}
