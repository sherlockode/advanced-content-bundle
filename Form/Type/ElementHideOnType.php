<?php

namespace Sherlockode\AdvancedContentBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ElementHideOnType extends AbstractType
{
    /**
     * @return string
     */
    public function getParent()
    {
        return ChoiceType::class;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'choices' => [
                'responsive.devices.mobile_portrait' => 'xs',
                'responsive.devices.mobile_landscape' => 'sm',
                'responsive.devices.tablet_portrait' => 'md',
                'responsive.devices.tablet_landscape' => 'lg',
                'responsive.devices.desktop' => 'xl',
            ],
            'multiple' => true,
            'expanded' => true,
        ]);
    }
}
