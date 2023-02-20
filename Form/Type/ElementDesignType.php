<?php

namespace Sherlockode\AdvancedContentBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\ColorType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;

class ElementDesignType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('margin_top', IntegerType::class, [
                'label' => false,
                'required' => false,
                'attr' => [
                    'data-css-property' => 'margin-top',
                    'data-controls' => 'margin',
                    'title' => 'configuration.design.margins.top',
                    'placeholder' => '-',
                ],
            ])
            ->add('margin_right', IntegerType::class, [
                'label' => false,
                'required' => false,
                'attr' => [
                    'data-css-property' => 'margin-right',
                    'data-follows' => 'margin',
                    'title' => 'configuration.design.margins.right',
                    'placeholder' => '-',
                ],
            ])
            ->add('margin_bottom', IntegerType::class, [
                'label' => false,
                'required' => false,
                'attr' => [
                    'data-css-property' => 'margin-bottom',
                    'data-follows' => 'margin',
                    'title' => 'configuration.design.margins.bottom',
                    'placeholder' => '-',
                ],
            ])
            ->add('margin_left', IntegerType::class, [
                'label' => false,
                'required' => false,
                'attr' => [
                    'data-css-property' => 'margin-left',
                    'data-follows' => 'margin',
                    'title' => 'configuration.design.margins.left',
                    'placeholder' => '-',
                ],
            ])
            ->add('border_top_width', IntegerType::class, [
                'label' => false,
                'required' => false,
                'attr' => [
                    'data-css-property' => 'border-top-width',
                    'data-controls' => 'border',
                    'title' => 'configuration.design.borders.top',
                    'placeholder' => '-',
                ],
            ])
            ->add('border_right_width', IntegerType::class, [
                'label' => false,
                'required' => false,
                'attr' => [
                    'data-css-property' => 'border-right-width',
                    'data-follows' => 'border',
                    'title' => 'configuration.design.borders.right',
                    'placeholder' => '-',
                ],
            ])
            ->add('border_bottom_width', IntegerType::class, [
                'label' => false,
                'required' => false,
                'attr' => [
                    'data-css-property' => 'border-bottom-width',
                    'data-follows' => 'border',
                    'title' => 'configuration.design.borders.bottom',
                    'placeholder' => '-',
                ],
            ])
            ->add('border_left_width', IntegerType::class, [
                'label' => false,
                'required' => false,
                'attr' => [
                    'data-css-property' => 'border-left-width',
                    'data-follows' => 'border',
                    'title' => 'configuration.design.borders.left',
                    'placeholder' => '-',
                ],
            ])
            ->add('padding_top', IntegerType::class, [
                'label' => false,
                'required' => false,
                'attr' => [
                    'data-css-property' => 'padding-top',
                    'data-controls' => 'padding',
                    'title' => 'configuration.design.paddings.top',
                    'placeholder' => '-',
                ],
            ])
            ->add('padding_right', IntegerType::class, [
                'label' => false,
                'required' => false,
                'attr' => [
                    'data-css-property' => 'padding-right',
                    'data-follows' => 'padding',
                    'title' => 'configuration.design.paddings.right',
                    'placeholder' => '-',
                ],
            ])
            ->add('padding_bottom', IntegerType::class, [
                'label' => false,
                'required' => false,
                'attr' => [
                    'data-css-property' => 'padding-bottom',
                    'data-follows' => 'padding',
                    'title' => 'configuration.design.paddings.bottom',
                    'placeholder' => '-',
                ],
            ])
            ->add('padding_left', IntegerType::class, [
                'label' => false,
                'required' => false,
                'attr' => [
                    'data-css-property' => 'padding-left',
                    'data-follows' => 'padding',
                    'title' => 'configuration.design.paddings.left',
                    'placeholder' => '-',
                ],
            ])
            ->add('border_top_left_radius', IntegerType::class, [
                'label' => false,
                'required' => false,
                'attr' => [
                    'data-css-property' => 'border-top-left-radius',
                    'data-controls' => 'border-radius',
                    'title' => 'configuration.design.radiuses.top_left',
                    'placeholder' => '-',
                ],
            ])
            ->add('border_top_right_radius', IntegerType::class, [
                'label' => false,
                'required' => false,
                'attr' => [
                    'data-css-property' => 'border-top-right-radius',
                    'data-follows' => 'border-radius',
                    'title' => 'configuration.design.radiuses.top_right',
                    'placeholder' => '-',
                ],
            ])
            ->add('border_bottom_right_radius', IntegerType::class, [
                'label' => false,
                'required' => false,
                'attr' => [
                    'data-css-property' => 'border-bottom-right-radius',
                    'data-follows' => 'border-radius',
                    'title' => 'configuration.design.radiuses.bottom_right',
                    'placeholder' => '-',
                ],
            ])
            ->add('border_bottom_left_radius', IntegerType::class, [
                'label' => false,
                'required' => false,
                'attr' => [
                    'data-css-property' => 'border-bottom-left-radius',
                    'data-follows' => 'border-radius',
                    'title' => 'configuration.design.radiuses.bottom_left',
                    'placeholder' => '-',
                ],
            ])
            ->add('border_color_select', ChoiceType::class, [
                'label' => 'configuration.design.border_color',
                'required' => false,
                'choices' => [
                    'configuration.design.colors.none' => 'none',
                    'configuration.design.colors.transparent' => 'transparent',
                    'configuration.design.colors.pick' => 'pick',
                ],
                'placeholder' => false,
                'attr' => ['data-css-property' => 'border-color', 'data-select-color' => 'border-color'],
            ])
            ->add('border_color', ColorType::class, [
                'label' => false,
                'required' => false,
                'attr' => ['data-css-property' => 'border-color'],
            ])
            ->add('border_style', ChoiceType::class, [
                'label' => 'configuration.design.border_style',
                'required' => false,
                'choices' => [
                    'configuration.design.border_styles.none' => 'none',
                    'configuration.design.border_styles.dotted' => 'dotted',
                    'configuration.design.border_styles.dashed' => 'dashed',
                    'configuration.design.border_styles.solid' => 'solid',
                    'configuration.design.border_styles.double' => 'double',
                    'configuration.design.border_styles.groove' => 'groove',
                    'configuration.design.border_styles.ridge' => 'ridge',
                    'configuration.design.border_styles.inset' => 'inset',
                    'configuration.design.border_styles.outset' => 'outset',
                ],
                'placeholder' => false,
                'attr' => ['data-css-property' => 'border-style'],
            ])
            ->add('background_color_select', ChoiceType::class, [
                'label' => 'configuration.design.background_color',
                'required' => false,
                'choices' => [
                    'configuration.design.colors.none' => 'none',
                    'configuration.design.colors.transparent' => 'transparent',
                    'configuration.design.colors.pick' => 'pick',
                ],
                'placeholder' => false,
                'attr' => ['data-css-property' => 'background-color', 'data-select-color' => 'background-color'],
            ])
            ->add('background_color', ColorType::class, [
                'label' => false,
                'required' => false,
                'attr' => ['data-css-property' => 'background-color'],
            ])
            ->add('simplify_controls', CheckboxType::class, [
                'label' => 'configuration.design.simplify_controls',
                'required' => false,
                'attr' => ['class' => 'simplify-controls'],
            ])
        ;
    }
}
