<?php

namespace Sherlockode\AdvancedContentBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FlexibleGroupCollectionType extends AbstractType
{
    public function getParent()
    {
        return CollectionType::class;
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['contentType'] = $options['contentType'];
        $view->vars['layouts'] = $options['layouts'];
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'label' => false,
            'entry_type' => FlexibleGroupType::class,
            'allow_add' => true,
            'by_reference' => false,
            'entry_options' => function (Options $options) {
                return ['contentType' => $options['contentType']];
            }
        ]);
        $resolver->setRequired(['contentType', 'layouts']);
    }

    public function getBlockPrefix()
    {
        return 'acb_flexible_group_collection';
    }
}
