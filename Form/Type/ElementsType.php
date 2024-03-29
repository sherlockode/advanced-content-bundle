<?php

namespace Sherlockode\AdvancedContentBundle\Form\Type;

use Sherlockode\AdvancedContentBundle\Manager\ConfigurationManager;
use Sherlockode\AdvancedContentBundle\Manager\ElementManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class ElementsType extends AbstractType
{
    /**
     * @var ElementManager
     */
    private $elementManager;

    /**
     * @var ConfigurationManager
     */
    private $configurationManager;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @param ElementManager       $elementManager
     * @param ConfigurationManager $configurationManager
     * @param TranslatorInterface  $translator
     */
    public function __construct(
        ElementManager $elementManager,
        ConfigurationManager $configurationManager,
        TranslatorInterface $translator
    ) {
        $this->elementManager = $elementManager;
        $this->configurationManager = $configurationManager;
        $this->translator = $translator;
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
                $field = $this->elementManager->getElementByCode($element['elementType']);
                $form->add($i++, ElementType::class, [
                    'label'      => $field->getFormFieldLabel(),
                    'element_type' => $field,
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

            // Remove existing children
            // And reset existing data
            // To prevent data mismatch on submit between existing and submitted data
            foreach ($form as $child) {
                $form->remove($child->getName());
            }
            $form->setData([]);

            foreach ($data as $name => $element) {
                if (!$form->has($name)) {
                    $form->add($name, ElementType::class, [
                        'element_type' => $this->elementManager->getElementByCode($element['elementType'] ?? 'text'),
                        'property_path' => '['.$name.']',
                    ]);
                }
            }
        });

        $builder->addEventListener(FormEvents::SUBMIT, function(FormEvent $event) use ($options) {
            $form = $event->getForm();
            $data = $event->getData();
            $data = array_values($data);

            if ($parentForm = $form->getParent()) {
                $parentElementType = $parentForm->has('elementType') ? $parentForm->get('elementType')->getData() : 'root';
                foreach ($data as $child) {
                    if ($parentElementType === 'root' && $child['elementType'] !== 'row') {
                        $form->addError(new FormError($this->translator->trans(
                            'layout_type.errors.invalid_element_in_root',
                            [],
                            'AdvancedContentBundle'
                        )));
                    }
                    if ($parentElementType === 'row' && $child['elementType'] !== 'column') {
                        $form->addError(new FormError($this->translator->trans(
                            'layout_type.errors.invalid_element_in_row',
                            [],
                            'AdvancedContentBundle'
                        )));
                    }
                    if ($parentElementType === 'column' &&
                        ($child['elementType'] === 'column' || $child['elementType'] === 'row')
                    ) {
                        $form->addError(new FormError($this->translator->trans(
                            'layout_type.errors.invalid_element_in_column',
                            [],
                            'AdvancedContentBundle'
                        )));
                    }
                }
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

    public function getBlockPrefix()
    {
        return 'acb_elements';
    }
}
