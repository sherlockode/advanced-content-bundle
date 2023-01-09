<?php

namespace Sherlockode\AdvancedContentBundle\Form\Type;

use Sherlockode\AdvancedContentBundle\Manager\ConfigurationManager;
use Sherlockode\AdvancedContentBundle\Manager\FieldManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ContentDataType extends AbstractType
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
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    /**
     * @param FieldManager          $fieldManager
     * @param ConfigurationManager  $configurationManager
     * @param UrlGeneratorInterface $urlGenerator
     */
    public function __construct(
        FieldManager $fieldManager,
        ConfigurationManager $configurationManager,
        UrlGeneratorInterface $urlGenerator
    ) {
        $this->fieldManager = $fieldManager;
        $this->configurationManager = $configurationManager;
        $this->urlGenerator = $urlGenerator;
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
            if (!$data) {
                $data = [];
            }

            $i = 0;
            foreach ($data as $name => $element) {
                $field = $this->fieldManager->getFieldTypeByCode($element['fieldType']);
                $form->add($i++, ElementType::class, [
                    'label'      => $field->getFormFieldLabel(),
                    'field_type' => $field,
                    'property_path' => '['.$name.']',
                ]);
            }
        });

        $builder->addEventListener(FormEvents::PRE_SUBMIT, function(FormEvent $event) use ($options) {
            $form = $event->getForm();
            $data = $event->getData();
            if (!is_array($data)) {
                $data = [];
            }

            foreach ($form as $child) {
                if (!isset($data[$child->getName()])) {
                    $form->remove($child->getName());
                }
            }

            foreach ($data as $name => $element) {
                if (!$form->has($name)) {
                    $form->add($name, ElementType::class, [
                        'field_type' => $this->fieldManager->getFieldTypeByCode($element['fieldType'] ?? 'text'),
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
            'row_attr' => [
                'class' => 'acb-elements-container',
                'data-edit-url' => $this->urlGenerator->generate('sherlockode_acb_content_field_form'),
            ],
        ]);
    }

    public function getBlockPrefix()
    {
        return 'acb_elements';
    }
}
