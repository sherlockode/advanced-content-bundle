<?php

namespace Sherlockode\AdvancedContentBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FieldsType extends AbstractType
{
    public function getBlockPrefix()
    {
        return 'acb_fields';
    }

    public function getParent()
    {
        return CollectionType::class;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'label' => false,
            'entry_type' => FieldType::class,
            'allow_add' => true,
            'allow_delete' => true,
            'by_reference' => false,
            'type_choices' => [],
            'entry_options' => function (Options $options) {
                return ['type_choices' => $options['type_choices']];
            }
        ]);
    }
}
