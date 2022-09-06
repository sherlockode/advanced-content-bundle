<?php

namespace Sherlockode\AdvancedContentBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RepeaterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($builder->hasAttribute('prototype')) {
            /** @var FormInterface $prototype */
            $prototype = $builder->getAttribute('prototype');
            if ($prototype->getConfig()->getCompound()) {
                $prototype->add('position', HiddenType::class);
            }
        }

        // add the position field to all collection children
        $positionCallback = function (FormEvent $event) {
            $form = $event->getForm();

            foreach ($form->all() as $child) {
                if ($child->getConfig()->getCompound()) {
                    $child->add('position', HiddenType::class);
                }
            }
        };
        $builder->addEventListener(FormEvents::PRE_SET_DATA, $positionCallback, -10);
        $builder->addEventListener(FormEvents::PRE_SUBMIT, $positionCallback, -10);

        // reorder the children array depending on the new position
        $builder->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) {
            $data = $event->getData();

            $orderedData = [];
            foreach ($data as $item) {
                if (!is_array($item)) {
                    return;
                }
                if (!isset($item['position'])) {
                    $item['position'] = 0;
                }
                $orderedData[] = $item;
            }
            usort($orderedData, function ($a, $b) {
                return $a['position'] <=> $b['position'];
            });

            $event->setData(array_values($orderedData));
        });
    }
    public function getParent()
    {
        return CollectionType::class;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'allow_add' => true,
            'allow_delete' => true,
            'by_reference' => false,
            'prototype_name' => '__group_name__',
            'entry_options' => ['block_prefix' => 'acb_field_collection_entry'],
        ]);
    }

    public function getBlockPrefix()
    {
        return 'acb_field_collection';
    }
}
