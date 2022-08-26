<?php

namespace Sherlockode\AdvancedContentBundle\FieldType;

use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Constraints\LessThanOrEqual;

class Number extends AbstractFieldType
{
    /**
     * Get options to apply on field value
     *
     * @return array
     */
    public function getFormFieldValueOptions()
    {
        $fieldOptions = [];

        $formFieldOptions = [];
        if (isset($fieldOptions['minValue'])) {
            $formFieldOptions['constraints'][] = new GreaterThanOrEqual(['value' => $fieldOptions['minValue']]);
        }
        if (isset($fieldOptions['maxValue'])) {
            $formFieldOptions['constraints'][] = new LessThanOrEqual(['value' => $fieldOptions['maxValue']]);
        }

        return $formFieldOptions;
    }

    /**
     * @return string
     */
    public function getFormFieldType()
    {
        return NumberType::class;
    }

    /**
     * Add field's options
     *
     * @param Form|FormBuilderInterface $builder
     *
     * @return void
     */
    public function addFieldOptions($builder)
    {
        $builder->get('options')
            ->add('minValue', IntegerType::class, ['required' => false])
            ->add('maxValue', IntegerType::class, ['required' => false])
        ;
    }

    /**
     * Get Field option names
     *
     * @return array
     */
    public function getFieldOptionNames()
    {
        return ['minValue', 'maxValue'];
    }

    /**
     * Get field's code
     *
     * @return string
     */
    public function getCode()
    {
        return 'number';
    }
}
