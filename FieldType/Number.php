<?php

namespace Sherlockode\AdvancedContentBundle\FieldType;

use Sherlockode\AdvancedContentBundle\Model\FieldInterface;
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
     * @param FieldInterface $field
     *
     * @return array
     */
    public function getFormFieldValueOptions(FieldInterface $field)
    {
        $fieldOptions = $this->getFieldOptions($field);

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
     * Get field's code
     *
     * @return string
     */
    public function getCode()
    {
        return 'number';
    }
}
