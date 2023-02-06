<?php

namespace Sherlockode\AdvancedContentBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;

class RowType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('columns_gap', IntegerType::class, [
                'label' => 'layout_type.row.config.columns_gap',
                'required' => false,
                'attr' => ['placeholder' => 'layout_type.row.config.columns_gap_placeholder'],
            ])
            ->add('mobile_reverse_columns', CheckboxType::class, [
                'label' => 'layout_type.row.config.mobile_reverse_columns',
                'required' => false,
            ])
            ->add('justify_content', ChoiceType::class, [
                'label' => 'layout_type.row.config.justify_content',
                'required' => false,
                'choices' => [
                    'layout_type.row.config.justify_contents.start' => 'start',
                    'layout_type.row.config.justify_contents.end' => 'end',
                    'layout_type.row.config.justify_contents.center' => 'center',
                    'layout_type.row.config.justify_contents.between' => 'between',
                    'layout_type.row.config.justify_contents.around' => 'around',
                ],
                'placeholder' => false,
            ])
        ;
    }
}
