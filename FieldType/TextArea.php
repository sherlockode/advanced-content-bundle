<?php

namespace Sherlockode\AdvancedContentBundle\FieldType;

use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class TextArea extends AbstractInputType
{
    /**
     * Get options to apply on element
     *
     * @return array
     */
    public function getFormElementOptions()
    {
        $fieldOptions = [];

        $formFieldOptions = parent::getFormElementOptions();
        if (isset($fieldOptions['nbRows'])) {
            $formFieldOptions['attr'] = ['rows' => $fieldOptions['nbRows']];
        }

        return $formFieldOptions;
    }

    /**
     * @return string
     */
    public function getFormFieldType()
    {
        return TextareaType::class;
    }

    /**
     * Get field's code
     *
     * @return string
     */
    public function getCode()
    {
        return 'textarea';
    }
}
