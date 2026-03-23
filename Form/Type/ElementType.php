<?php

declare(strict_types=1);

namespace Sherlockode\AdvancedContentBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ElementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $options['element_type']->buildContentElement($builder);
        $builder->add('extra', FormType::class, [
            'label' => false,
        ]);
        $builder->get('extra')->add('advanced', ElementAdvancedType::class, [
            'label' => false,
        ]);
        $builder->get('extra')->add('design', ElementDesignType::class, [
            'label' => false,
        ]);
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $view->vars['panel_label'] = $options['element_type']->getFormFieldLabel();
        $view->vars['field_icon'] = $options['element_type']->getIconClass();
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setRequired(['element_type']);
        $resolver->setDefaults([
            'translation_domain' => 'AdvancedContentBundle',
        ]);
    }

    public function getBlockPrefix()
    {
        return 'acb_element';
    }
}
