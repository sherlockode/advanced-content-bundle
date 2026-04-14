<?php

declare(strict_types=1);

namespace Sherlockode\AdvancedContentBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * FormType wrapper for non-compound forms inside RepeaterType
 * Used in order to be able to add the "position" field.
 */
class RepeatedChildWrappedType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('wrapped_child', $options['child_form'], $options['child_options'])
            ->add('position', HiddenType::class, ['data' => $options['position']])
        ;

        $dataCallback = function (FormEvent $event): void {
            $data = $event->getData();

            $data = ['wrapped_child' => $data];

            $event->setData($data);
        };

        $builder->addEventListener(FormEvents::PRE_SET_DATA, $dataCallback, -5);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'position' => 0,
            'child_options' => [],
        ]);
        $resolver->setRequired(['child_form']);
        $resolver->setNormalizer('child_options', function (Options $options, array $value): array {
            unset($value['property_path']);

            return $value;
        });
    }
}
