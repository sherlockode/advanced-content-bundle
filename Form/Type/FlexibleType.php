<?php

namespace Sherlockode\AdvancedContentBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FlexibleType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'label' => 'field_type.flexible.layout_list',
            'entry_type' => LayoutType::class,
            'allow_add' => true,
            'allow_delete' => true,
            'by_reference' => false,
            'translation_domain' => 'AdvancedContentBundle',
            'block_name' => 'acb_flexible',
            'prototype_name' => '__layout_name__',
        ]);
    }

    public function getParent()
    {
        return CollectionType::class;
    }

    public function getBlockPrefix()
    {
        return 'acb_flexible';
    }
}
