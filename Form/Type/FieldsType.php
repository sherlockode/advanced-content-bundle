<?php

namespace Sherlockode\AdvancedContentBundle\Form\Type;

use Sherlockode\AdvancedContentBundle\Manager\ConfigurationManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class FieldsType extends AbstractType
{
    /**
     * @var ConfigurationManager
     */
    private $configurationManager;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @param ConfigurationManager $configurationManager
     * @param TranslatorInterface  $translator
     */
    public function __construct($configurationManager, $translator)
    {
        $this->configurationManager = $configurationManager;
        $this->translator = $translator;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $fieldClass = $this->configurationManager->getEntityClass('field');
        $fieldTypeChoices = $options['type_choices'];
        $translator = $this->translator;
        $builder->addEventListener(
            FormEvents::PRE_SUBMIT,
            function (FormEvent $event) use ($fieldClass, $fieldTypeChoices, $translator) {
                $form = $event->getForm();
                $data = $event->getData() ?? [];

                $duplicatedSlugs = [];
                $slugs = [];
                foreach ($data as $value) {
                    $slug = $value['slug'];
                    if (in_array($slug, $slugs)) {
                        $duplicatedSlugs[] = $slug;
                        continue;
                    }

                    if (isset($value['children'])) {
                        foreach ($value['children'] as $child) {
                            if (isset($child['children'])) {
                                $childrenSlugs = [];
                                foreach ($child['children'] as $field) {
                                    $fieldSlug = $field['slug'];
                                    if (in_array($fieldSlug, $childrenSlugs)) {
                                        $duplicatedSlugs[] = $fieldSlug;
                                        continue;
                                    }
                                    $childrenSlugs[] = $fieldSlug;
                                }
                            }
                        }
                    }
                    $slugs[] = $slug;
                }

                $duplicatedSlugs = array_unique($duplicatedSlugs);
                if (count($duplicatedSlugs) > 0) {
                    foreach ($duplicatedSlugs as $slug) {
                        $form->addError(new FormError(
                            $translator->trans('field_type.errors.duplicated_slug_detail', ['%slug%' => $slug], 'AdvancedContentBundle')
                        ));
                    }
                    return;
                }

                foreach ($form as $child) {
                    if (!isset($data[$child->getName()])) {
                        $form->remove($child->getName());
                    }
                }
                foreach ($data as $key => $value) {
                    if (!$form->has($key)) {
                        $form->add($key, FieldType::class, [
                            'type_choices' => $fieldTypeChoices,
                            'data_class'   => $fieldClass,
                        ]);
                    }
                }
                $event->setData($data);
            }
        );

        $builder->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) {
            $form = $event->getForm();
            $data = $event->getData();

            $toDelete = [];
            foreach ($data as $name => $value) {
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

    public function getBlockPrefix()
    {
        return 'acb_fields';
    }

    public function getParent()
    {
        return CollectionType::class;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'label' => false,
            'entry_type' => FieldType::class,
            'allow_add' => true,
            'allow_delete' => true,
            'by_reference' => false,
            'type_choices' => [],
            'entry_options' => function (Options $options) {
                return ['type_choices' => $options['type_choices']];
            }
        ]);
    }
}
