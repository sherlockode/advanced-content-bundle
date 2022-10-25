<?php

namespace Sherlockode\AdvancedContentBundle\FieldType;

use Sherlockode\AdvancedContentBundle\Form\Type\WysiwygType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Form;

class Wysiwyg extends AbstractFieldType
{
    /**
     * @return string
     */
    public function getFormFieldType()
    {
        return WysiwygType::class;
    }

    protected function getDefaultIconClass()
    {
        return 'fa-solid fa-text-height';
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
