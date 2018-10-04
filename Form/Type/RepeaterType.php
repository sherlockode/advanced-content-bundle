<?php

namespace Sherlockode\AdvancedContentBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RepeaterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addViewTransformer(new CallbackTransformer(function ($data) {
            if ($data === null) {
                return null;
            }
            if (!is_array($data) && !$data instanceof \ArrayAccess) {
                return null;
            }
            return $data[0];
        }, function ($data) {
            return  [$data];
        }));
    }

    public function getParent()
    {
        return LayoutType::class;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'label' => 'field_type.repeater.field_list',
            'translation_domain' => 'AdvancedContentBundle',
        ]);
    }
}
