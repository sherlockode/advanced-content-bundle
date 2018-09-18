<?php

namespace Sherlockode\AdvancedContentBundle\Form\Type;

use Sherlockode\AdvancedContentBundle\Manager\ConfigurationManager;
use Sherlockode\AdvancedContentBundle\Manager\FieldManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FieldType extends AbstractType
{
    /**
     * @var FieldManager
     */
    private $fieldManager;

    /**
     * @var ConfigurationManager
     */
    private $configurationManager;

    /**
     * @param FieldManager         $fieldManager
     * @param ConfigurationManager $configurationManager
     */
    public function __construct(FieldManager $fieldManager, ConfigurationManager $configurationManager)
    {
        $this->fieldManager = $fieldManager;
        $this->configurationManager = $configurationManager;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('type', ChoiceType::class, [
                'label' => 'content_type.form.field.type',
                'choices' => $options['type_choices'],
                'required' => true,
            ])
            ->add('slug', TextType::class, [
                'label' => 'content_type.form.field.slug',
            ])
            ->add('name', TextType::class, [
                'required' => true,
                'label' => 'content_type.form.field.name',
            ])
            ->add('required', ChoiceType::class, [
                'label' => 'content_type.form.field.required',
                'choices' => [
                    'Yes' => true,
                    'No' => false,
                ]
            ])
            ->add('sortOrder', IntegerType::class, [
                'label' => 'content_type.form.field.sort_order',
                'required' => true,
            ])
            ->add('hint', TextareaType::class, [
                'label' => 'content_type.form.field.hint',
                'required' => false,
            ])
        ;

        $builder->add('options', FormType::class, ['label' => 'content_type.form.field.options']);
        $options['field_type']->addFieldOptions($builder);

        $builder->addEventListener(
            FormEvents::PRE_SUBMIT,
            function (FormEvent $event) use ($options) {
                $form = $event->getForm();
                $child = $event->getData();

                $this->fieldManager->getFieldTypeByCode($child['type'])->addFieldOptions($form);
            }
        );

        $builder->addEventListener(
            FormEvents::SUBMIT,
            function (FormEvent $event) use ($options) {
                $field = $event->getData();
                $this->fieldManager->getFieldTypeByCode($field->getType())->clearOptions($field);
            }
        );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'type_choices' => [],
            'translation_domain' => 'AdvancedContentBundle',
            'data_class' => $this->configurationManager->getEntityClass('field'),
        ]);
        $resolver->setRequired(['field_type']);
    }

    public function getBlockPrefix()
    {
        return 'acb_field';
    }
}
