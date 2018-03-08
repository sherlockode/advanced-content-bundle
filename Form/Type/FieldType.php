<?php

namespace Sherlockode\AdvancedContentBundle\Form\Type;

use Sherlockode\AdvancedContentBundle\FieldType\Text;
use Sherlockode\AdvancedContentBundle\Model\FieldInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FieldType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('type', ChoiceType::class, [
                'choices' => $options['type_choices'],
                'required' => true,
            ])
            ->add('name', TextType::class, ['required' => true])
            ->add('slug', TextType::class, ['required' => true])
            ->add('isRequired', ChoiceType::class, [
                'choices' => [
                    'Yes' => true,
                    'No' => false,
                ]
            ]);
        $options['field_type']->buildContentTypeFieldOptions($builder, $options['field']);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['field_type' => Text::class, 'field' => FieldInterface::class, 'type_choices' => []]);
    }
}
