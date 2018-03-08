<?php

namespace Sherlockode\AdvancedContentBundle\FieldType;

use Sherlockode\AdvancedContentBundle\Model\FieldInterface;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;

class Text extends AbstractFieldType
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

        return $formFieldOptions;
    }

    /**
     * @return string
     */
    public function getFormFieldType()
    {
        return TextType::class;
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
        ;
    }
}
