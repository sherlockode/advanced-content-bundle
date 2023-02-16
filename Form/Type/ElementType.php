<?php

namespace Sherlockode\AdvancedContentBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ElementType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
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

        $builder->addEventListener(FormEvents::PRE_SUBMIT, function(FormEvent $event) use ($options) {
            if ($options['is_post_json']) {
                $event->setData(json_decode($event->getData() ?? '[]', true));
            }
        });
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['panel_label'] = $options['element_type']->getFormFieldLabel();
        $view->vars['field_icon'] = $options['element_type']->getIconClass();
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired(['element_type']);
        $resolver->setDefaults([
            'translation_domain' => 'AdvancedContentBundle',
            'is_post_json' => false
        ]);
    }

    public function getBlockPrefix()
    {
        return 'acb_element';
    }
}
