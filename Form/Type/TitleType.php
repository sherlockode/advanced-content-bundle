<?php

namespace Sherlockode\AdvancedContentBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class TitleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $choices = [];
        foreach (range(1, 6) as $level) {
            $choices[$level] = $level;
        }

        $builder
            ->add('text', TextType::class, [
                'label' => 'field_type.title.text',
                'constraints' => [
                    new NotBlank(null, null, null, null, $options['validation_groups']),
                ],
            ])
            ->add('level', ChoiceType::class, [
                'choices' => $choices,
                'label' => 'field_type.title.level',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'translation_domain' => 'AdvancedContentBundle',
        ]);
    }
}
