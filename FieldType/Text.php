<?php

namespace Sherlockode\AdvancedContentBundle\FieldType;

use Sherlockode\AdvancedContentBundle\Model\Field;
use Symfony\Component\Validator\Constraints\Length;

class Text
{
    /**
     * Get field available options
     *
     * @return array
     */
    public function getOptions()
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
     * Get options setup for given field
     *
     * @param Field $field
     *
     * @return array
     */
    public function getFieldOptions(Field $field)
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
