<?php

namespace Sherlockode\AdvancedContentBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
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

        $builder->get('value')->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) {
            $data = $event->getData();
            $form = $event->getForm();
            if ($form->getConfig()->getCompound()) {
                $cleanData = [];
                // remove possibly obsolete data if the form was changed
                foreach ($form as $key => $child) {
                    if (isset($data[$key])) {
                        $cleanData[$key] = $data[$key];
                    }
                }

                $event->setData($cleanData);
            }
        });
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
        $resolver->setDefaults([
            'translation_domain' => 'AdvancedContentBundle',
        ]);
    }

    public function getBlockPrefix()
    {
        return 'acb_field_value';
    }
}
