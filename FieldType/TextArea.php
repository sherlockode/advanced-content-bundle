<?php

namespace Sherlockode\AdvancedContentBundle\FieldType;

use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Form;

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
     * Add field's options
     *
     * @param Form|FormBuilderInterface $builder
     *
     * @return void
     */
    public function addFieldOptions($builder)
    {
        parent::addFieldOptions($builder);
        $builder->get('options')
            ->add('nbRows', IntegerType::class, [
                'label' => 'field_type.textarea.nb_rows.label',
                'required' => false,
            ])
        ;
    }

    /**
     * Get Field option names
     *
     * @return array
     */
    public function getFieldOptionNames()
    {
        return array_merge(parent::getFieldOptionNames(), ['nbRows']);
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
