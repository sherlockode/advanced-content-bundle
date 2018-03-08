<?php

namespace Sherlockode\AdvancedContentBundle\FieldType;

use Sherlockode\AdvancedContentBundle\Model\FieldInterface;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;

class TextArea extends AbstractFieldType
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
        if (isset($fieldOptions['minLength'])) {
            $formFieldOptions['constraints'][] = new Length(['min' => $fieldOptions['minLength']]);
        }
        if (isset($fieldOptions['maxLength'])) {
            $formFieldOptions['constraints'][] = new Length(['max' => $fieldOptions['maxLength']]);
        }
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
     * Add field's options
     *
     * @param FormBuilderInterface $builder
     * @param FieldInterface       $field
     *
     * @return void
     */
    public function addFieldOptions(FormBuilderInterface $builder, FieldInterface $field)
    {
        $builder->get('options')
            ->add('minLength', IntegerType::class)
            ->add('maxLength', IntegerType::class)
            ->add('nbRows', IntegerType::class)
        ;
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
