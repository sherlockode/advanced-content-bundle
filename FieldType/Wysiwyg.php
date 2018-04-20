<?php

namespace Sherlockode\AdvancedContentBundle\FieldType;

use Ivory\CKEditorBundle\Form\Type\CKEditorType;
use Sherlockode\AdvancedContentBundle\Model\FieldInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;

class Wysiwyg extends AbstractFieldType
{
    /**
     * Get available options for given field type
     *
     * @return array
     */
    public function getFieldTypeOptions()
    {
        return [
            'toolbar' => [
                'label' => 'Toolbar',
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
     * @param FormBuilderInterface $builder
     * @param FieldInterface       $field
     *
     * @return void
     */
    public function addFieldOptions(FormBuilderInterface $builder, FieldInterface $field)
    {
        $builder->get('options')
            ->add('toolbar', ChoiceType::class, [
                'choices' => [
                    'Basic' => 'basic',
                    'Standard' => 'standard',
                    'Full' => 'full'
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
        return 'wysiwyg';
    }
}
