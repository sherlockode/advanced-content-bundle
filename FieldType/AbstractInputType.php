<?php

namespace Sherlockode\AdvancedContentBundle\FieldType;

use Symfony\Component\Validator\Constraints\Length;

abstract class AbstractInputType extends AbstractFieldType
{
    protected function getDefaultIconClass()
    {
        return 'fa-solid fa-font';
    }

    /**
     * Get options to apply on element
     *
     * @return array
     */
    public function getFormElementOptions()
    {
        $fieldOptions = [];

        $formFieldOptions = [];
        if (isset($fieldOptions['minLength'])) {
            $formFieldOptions['constraints'][] = new Length(['min' => $fieldOptions['minLength']]);
        }
        if (isset($fieldOptions['maxLength'])) {
            $formFieldOptions['constraints'][] = new Length(['max' => $fieldOptions['maxLength']]);
        }

        return $formFieldOptions;
    }

    /**
     * @return string
     */
    public function getFieldGroup()
    {
        return 'simple';
    }
}
