<?php

namespace Sherlockode\AdvancedContentBundle\FieldType;

use Sherlockode\AdvancedContentBundle\Model\FieldInterface;
use Symfony\Component\Validator\Constraints\Length;

class Text extends AbstractFieldType
{
    /**
     * Get available options for given field type
     *
     * @return array
     */
    public function getFieldTypeOptions()
    {
        return [
            'minLength' => [
                'label' => 'Min Length',
                'type'  => 'text'
            ],
            'maxLength' => [
                'label' => 'Max Length',
                'type'  => 'text'
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
        $fieldOptions = $field->getOptions();

        $formFieldOptions = [];
        if (isset($fieldOptions['minLength'])) {
            $formFieldOptions['constraints'][] = new Length(['min' => $fieldOptions['minLength']]);
        }
        if (isset($fieldOptions['maxLength'])) {
            $formFieldOptions['constraints'][] = new Length(['max' => $fieldOptions['maxLength']]);
        }

        return $formFieldOptions;
    }
}
