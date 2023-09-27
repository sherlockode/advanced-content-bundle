<?php

namespace Sherlockode\AdvancedContentBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;

class ColumnType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $sizeOptions = [
            'layout_type.column.config.sizes.1_column' => 1,
            'layout_type.column.config.sizes.2_column' => 2,
            'layout_type.column.config.sizes.3_column' => 3,
            'layout_type.column.config.sizes.4_column' => 4,
            'layout_type.column.config.sizes.5_column' => 5,
            'layout_type.column.config.sizes.6_column' => 6,
            'layout_type.column.config.sizes.7_column' => 7,
            'layout_type.column.config.sizes.8_column' => 8,
            'layout_type.column.config.sizes.9_column' => 9,
            'layout_type.column.config.sizes.10_column' => 10,
            'layout_type.column.config.sizes.11_column' => 11,
            'layout_type.column.config.sizes.12_column' => 12,
            'layout_type.column.config.sizes.column' => '-',
            'layout_type.column.config.sizes.column_auto' => 'auto',
        ];

        $offsetOptions = [
            'layout_type.column.config.offsets.no_offset' => 0,
            'layout_type.column.config.offsets.1_column' => 1,
            'layout_type.column.config.offsets.2_column' => 2,
            'layout_type.column.config.offsets.3_column' => 3,
            'layout_type.column.config.offsets.4_column' => 4,
            'layout_type.column.config.offsets.5_column' => 5,
            'layout_type.column.config.offsets.6_column' => 6,
            'layout_type.column.config.offsets.7_column' => 7,
            'layout_type.column.config.offsets.8_column' => 8,
            'layout_type.column.config.offsets.9_column' => 9,
            'layout_type.column.config.offsets.10_column' => 10,
            'layout_type.column.config.offsets.11_column' => 11,
        ];

        $builder
            ->add('size', ChoiceType::class, [
                'label' => 'layout_type.column.config.size',
                'choices' => $sizeOptions,
                'required' => true,
            ])
            ->add('offset', ChoiceType::class, [
                'label' => 'layout_type.column.config.offset',
                'choices' => $offsetOptions,
                'placeholder' => false,
                'required' => false,
            ])
            ->add('size_sm', ChoiceType::class, [
                'label' => 'layout_type.column.config.size',
                'choices' => $sizeOptions,
                'placeholder' => 'layout_type.column.config.inherit_from_smaller',
                'required' => false,
            ])
            ->add('offset_sm', ChoiceType::class, [
                'label' => 'layout_type.column.config.offset',
                'choices' => $offsetOptions,
                'placeholder' => 'layout_type.column.config.inherit_from_smaller',
                'required' => false,
            ])
            ->add('size_md', ChoiceType::class, [
                'label' => 'layout_type.column.config.size',
                'choices' => $sizeOptions,
                'placeholder' => 'layout_type.column.config.inherit_from_smaller',
                'required' => false,
            ])
            ->add('offset_md', ChoiceType::class, [
                'label' => 'layout_type.column.config.offset',
                'choices' => $offsetOptions,
                'placeholder' => 'layout_type.column.config.inherit_from_smaller',
                'required' => false,
            ])
            ->add('size_lg', ChoiceType::class, [
                'label' => 'layout_type.column.config.size',
                'choices' => $sizeOptions,
                'placeholder' => 'layout_type.column.config.inherit_from_smaller',
                'required' => false,
            ])
            ->add('offset_lg', ChoiceType::class, [
                'label' => 'layout_type.column.config.offset',
                'choices' => $offsetOptions,
                'placeholder' => 'layout_type.column.config.inherit_from_smaller',
                'required' => false,
            ])
            ->add('size_xl', ChoiceType::class, [
                'label' => 'layout_type.column.config.size',
                'choices' => $sizeOptions,
                'placeholder' => 'layout_type.column.config.inherit_from_smaller',
                'required' => false,
            ])
            ->add('offset_xl', ChoiceType::class, [
                'label' => 'layout_type.column.config.offset',
                'choices' => $offsetOptions,
                'placeholder' => 'layout_type.column.config.inherit_from_smaller',
                'required' => false,
            ])
        ;
    }

    public function getBlockPrefix()
    {
        return 'acb_column_config';
    }
}
