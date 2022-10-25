<?php

namespace Sherlockode\AdvancedContentBundle\FieldType;

use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class TextArea extends AbstractInputType
{
    /**
     * @return string
     */
    public function getFrontTemplate()
    {
        return '@SherlockodeAdvancedContent/Field/front/text.html.twig';
    }

    /**
     * Get options to apply on field value
     *
     * @return array
     */
    public function getFormFieldValueOptions()
    {
        $fieldOptions = [];

        $formFieldOptions = parent::getFormFieldValueOptions();
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
