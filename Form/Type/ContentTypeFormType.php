<?php

namespace Sherlockode\AdvancedContentBundle\Form\Type;

use Sherlockode\AdvancedContentBundle\Form\DataTransformer\FieldsTransformer;
use Sherlockode\AdvancedContentBundle\Manager\ConfigurationManager;
use Sherlockode\AdvancedContentBundle\Manager\ContentTypeManager;
use Sherlockode\AdvancedContentBundle\Manager\FieldManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\TranslatorInterface;

class ContentTypeFormType extends AbstractType
{
    /**
     * @var ContentTypeManager
     */
    private $contentTypeManager;

    /**
     * @var FieldManager
     */
    private $fieldManager;

    /**
     * @var ConfigurationManager
     */
    private $configurationManager;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @param ContentTypeManager   $contentTypeManager
     * @param FieldManager         $fieldManager
     * @param ConfigurationManager $configurationManager
     * @param TranslatorInterface  $translator
     */
    public function __construct(
        ContentTypeManager $contentTypeManager,
        FieldManager $fieldManager,
        ConfigurationManager $configurationManager,
        TranslatorInterface $translator
    ) {
        $this->contentTypeManager = $contentTypeManager;
        $this->fieldManager = $fieldManager;
        $this->configurationManager = $configurationManager;
        $this->translator = $translator;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, ['label' => 'content_type.form.name'])
        ;

        if (!$options['contentType']->getId()) {
            return;
        }

        $fields = $this->contentTypeManager->getOrderedFields($options['contentType']);
        $fieldsBuilder = $builder->create('fields', FieldsType::class, [
            'label' => 'content_type.form.fields',
            'translation_domain' => 'AdvancedContentBundle',
        ]);

        $fieldTypeChoices = $this->fieldManager->getFieldTypeFormChoices();
        $fieldClass = $this->configurationManager->getEntityClass('field');
        foreach ($fields as $field) {
            if ($field->getLayout()) {
                // do not add fields which are not top-level
                continue;
            }
            $fieldsBuilder->add($field->getSlug() ?? $field->getName(), FieldType::class, [
                'type_choices' => $fieldTypeChoices,
                'data_class'   => $fieldClass,
                'data'         => $field,
            ]);
        }
        $builder->add($fieldsBuilder);
        $translator = $this->translator;
        $fieldsBuilder->addEventListener(
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
                                foreach ($child['children'] as $field) {
                                    $fieldSlug = $field['slug'];
                                    if (in_array($fieldSlug, $slugs)) {
                                        $duplicatedSlugs[] = $fieldSlug;
                                        continue;
                                    }
                                    $slugs[] = $fieldSlug;
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
        $fieldsBuilder->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) {
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
        $fieldsBuilder
            ->addModelTransformer(new FieldsTransformer($options['contentType']))
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'translation_domain' => 'AdvancedContentBundle',
        ]);
        $resolver->setRequired(['contentType']);
    }
}
