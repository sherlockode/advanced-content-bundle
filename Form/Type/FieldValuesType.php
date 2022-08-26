<?php

namespace Sherlockode\AdvancedContentBundle\Form\Type;

use Sherlockode\AdvancedContentBundle\Manager\ConfigurationManager;
use Sherlockode\AdvancedContentBundle\Manager\FieldManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FieldValuesType extends AbstractType
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
    public function __construct(
        FieldManager $fieldManager,
        ConfigurationManager $configurationManager
    ) {
        $this->fieldManager = $fieldManager;
        $this->configurationManager = $configurationManager;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvent $event) use ($options) {
            $data = $event->getData();
            $form = $event->getForm();

            $i = 0;
            foreach ($data as $name => $fieldValue) {
                $field = $this->fieldManager->getFieldTypeByCode($fieldValue->getFieldType());
                $form->add($i++, FieldValueType::class, [
                    'label'      => $field->getFormFieldLabel(),
                    'field_type' => $field,
                    'data_class' => $this->configurationManager->getEntityClass('field_value'),
                    'property_path' => '['.$name.']',
                ]);
            }
        });

        $builder->addEventListener(FormEvents::PRE_SUBMIT, function(FormEvent $event) use ($options) {
            $form = $event->getForm();
            $data = $event->getData();

            foreach ($form as $child) {
                if (!isset($data[$child->getName()])) {
                    $form->remove($child->getName());
                }
            }

            foreach ($data as $name => $value) {
                if (!$form->has($name)) {
                    $form->add($name, FieldValueType::class, [
                        'field_type' => $this->fieldManager->getFieldTypeByCode($value['type'] ?? 'text'),
                        'data_class' => $this->configurationManager->getEntityClass('field_value'),
                        'property_path' => '['.$name.']',
                    ]);
                }
            }
        });


        $builder->addEventListener(FormEvents::SUBMIT, function(FormEvent $event) use ($options) {
            $form = $event->getForm();
            $data = $event->getData();

            $toDelete = [];
            foreach ($data as $name => $child) {
                if (!$form->has($name)) {
                    $toDelete[] = $name;
                }
            }

            foreach ($toDelete as $name) {
                unset($data[$name]);
            }

            $event->setData($data);
        });
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'translation_domain' => 'AdvancedContentBundle',
            'by_reference' => false,
        ]);
    }
}
