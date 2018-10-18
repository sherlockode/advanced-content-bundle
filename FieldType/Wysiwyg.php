<?php

namespace Sherlockode\AdvancedContentBundle\FieldType;

use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Sherlockode\AdvancedContentBundle\Model\FieldInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Form;

class Wysiwyg extends AbstractFieldType
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

        if (!isset($fieldOptions['toolbar'])) {
            throw new \RuntimeException("Missing mandatory option toolbar.");
        }

        $formFieldOptions = ['config' => ['toolbar' => $fieldOptions['toolbar']]];

        return $formFieldOptions;
    }

    /**
     * @return string
     */
    public function getFormFieldType()
    {
        return CKEditorType::class;
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
            ->add('toolbar', ChoiceType::class, [
                'label' => 'field_type.wysiwyg.toolbar.label',
                'choices' => [
                    'field_type.wysiwyg.toolbar.basic' => 'basic',
                    'field_type.wysiwyg.toolbar.standard' => 'standard',
                    'field_type.wysiwyg.toolbar.full' => 'full'
                ]
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
        return ['toolbar'];
    }

    /**
     * Get field's code
     *
     * @return string
     */
    public function getCode()
    {
        return 'wysiwyg';
    }
}
