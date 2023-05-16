<?php

namespace Sherlockode\AdvancedContentBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class ElementAdvancedType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('class', TextType::class, [
                'label' => 'configuration.advanced.class',
                'required' => false,
            ])
            ->add('id', TextType::class, [
                'label' => 'configuration.advanced.id',
                'required' => false,
            ])
            ->add('hide_on', ElementHideOnType::class, [
                'label' => 'configuration.advanced.hide_on',
                'required' => false,
            ])
        ;
    }
}
