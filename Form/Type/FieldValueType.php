<?php

namespace Sherlockode\AdvancedContentBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FieldValueType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $options['field_type']->buildContentFieldValue($builder);
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['panel_label'] = 'field_type.' . $options['field_type']->getCode() . '.label';
        $view->vars['field_icon'] = $options['field_type']->getIconClass();
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired(['field_type']);
    }

    public function getBlockPrefix()
    {
        return 'acb_field_value';
    }
}
