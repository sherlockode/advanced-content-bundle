<?php

namespace Sherlockode\AdvancedContentBundle\FieldType;

use Sherlockode\AdvancedContentBundle\Form\DataTransformer\StringToArrayTransformer;
use Sherlockode\AdvancedContentBundle\Model\FieldInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Form;

class Link extends AbstractFieldType
{
    /**
     * Get available options for given field type
     *
     * @return array
     */
    public function getFieldTypeOptions()
    {
        return [
            'target' => [
                'label' => 'Target',
                'type' => 'choices'
            ]
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
        return [];
    }

    /**
     * @return string
     */
    public function getFormFieldType()
    {
        return FormType::class;
    }

    /**
     * Add field value's field(s) to content form
     *
     * @param FormBuilderInterface $builder
     * @param FieldInterface       $field
     *
     * @return void
     */
    public function buildContentFieldValue(FormBuilderInterface $builder, FieldInterface $field)
    {
        parent::buildContentFieldValue($builder, $field);

        $options = [];
        if ($field->isIsRequired()) {
            $options['required'] = true;
        }

        $builder->get('value')
            ->add('href', UrlType::class, $options)
            ->add('anchor', TextType::class, $options)
            ->addModelTransformer(new StringToArrayTransformer())
        ;
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
            ->add('target', ChoiceType::class, [
                'choices' => [
                    'Blank' => 'blank',
                    'Self' => 'self'
                ]
            ])
        ;
    }

    /**
     * Get field's code
     *
     * @return string
     */
    public function getCode()
    {
        return 'link';
    }
}
