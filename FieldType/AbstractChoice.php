<?php

namespace Sherlockode\AdvancedContentBundle\FieldType;

use Sherlockode\AdvancedContentBundle\Model\FieldInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

abstract class AbstractChoice extends AbstractFieldType
{
    /**
     * @var bool
     */
    protected $isMultipleChoice;

    /**
     * Get available options for given field type
     *
     * @return array
     */
    public function getFieldTypeOptions()
    {
        return [
            'choices' => [
                'label' => 'Choices',
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

        $formFieldOptions = [];
        $formFieldOptions['choices'] = array_flip($fieldOptions['choices']);
        $formFieldOptions['expanded'] = true;
        $formFieldOptions['multiple'] = $this->isMultipleChoice;

        return $formFieldOptions;
    }

    /**
     * @return string
     */
    public function getFormFieldType()
    {
        return ChoiceType::class;
    }
}
