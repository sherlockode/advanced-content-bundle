<?php

namespace Sherlockode\AdvancedContentBundle\Manager;

use Sherlockode\AdvancedContentBundle\Form\DataTransformer\FieldsTransformer;
use Sherlockode\AdvancedContentBundle\Form\Type\FieldsType;
use Sherlockode\AdvancedContentBundle\Form\Type\FieldType;
use Sherlockode\AdvancedContentBundle\Form\Type\FieldValueType;
use Sherlockode\AdvancedContentBundle\Form\DataTransformer\FieldValuesTransformer;
use Sherlockode\AdvancedContentBundle\Model\ContentTypeInterface;
use Sherlockode\AdvancedContentBundle\Model\FieldInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Translation\TranslatorInterface;

class FormBuilderManager
{
    /**
     * @var FieldManager
     */
    private $fieldManager;

    /**
     * @var ContentManager
     */
    private $contentManager;

    /**
     * @var ConfigurationManager
     */
    private $configurationManager;

    /**
     * @var ContentTypeManager
     */
    private $contentTypeManager;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * FormBuilderManager constructor.
     *
     * @param ContentManager       $contentManager
     * @param FieldManager         $fieldManager
     * @param ConfigurationManager $configurationManager
     * @param ContentTypeManager   $contentTypeManager
     * @param TranslatorInterface  $translator
     */
    public function __construct(
        ContentManager $contentManager,
        FieldManager $fieldManager,
        ConfigurationManager $configurationManager,
        ContentTypeManager $contentTypeManager,
        TranslatorInterface $translator
    ) {
        $this->contentManager = $contentManager;
        $this->fieldManager = $fieldManager;
        $this->configurationManager = $configurationManager;
        $this->contentTypeManager = $contentTypeManager;
        $this->translator = $translator;
    }

    /**
     * Build custom form for Content edit
     *
     * @param FormBuilderInterface $builder
     * @param ContentTypeInterface $contentType
     */
    public function buildContentForm(FormBuilderInterface $builder, ContentTypeInterface $contentType)
    {
        $fields = $this->contentTypeManager->getOrderedFields($contentType);

        $fieldsBuilder = $builder->create('fieldValues', FormType::class, [
            'label' => 'content.form.field_values',
            'translation_domain' => 'AdvancedContentBundle',
        ]);
        foreach ($fields as $field) {
            if ($field->getLayout()) {
                // do not add fields which are not top-level
                continue;
            }
            $fieldsBuilder->add($field->getId(), FieldValueType::class, [
                'label'      => $field->getName(),
                'required'   => $field->isRequired(),
                'field_type' => $this->fieldManager->getFieldType($field),
                'field'      => $field,
                'data_class' => $this->configurationManager->getEntityClass('field_value'),
                'translation_domain' => false,
            ]);
        }
        $builder->add($fieldsBuilder);
        $fieldsBuilder
            ->addViewTransformer(new FieldValuesTransformer($this->contentManager, $contentType));
    }

    /**
     * Build form for Content create
     *
     * @param FormBuilderInterface $builder
     */
    public function buildCreateContentForm(FormBuilderInterface $builder)
    {
        $builder->add(
            'contentType',
            EntityType::class,
            [
                'class'        => $this->configurationManager->getEntityClass('content_type'),
                'choice_label' => 'name'
            ]
        );
    }

    /**
     * Build custom form for Content Type edit
     *
     * @param FormBuilderInterface $builder
     * @param ContentTypeInterface $contentType
     */
    public function buildContentTypeForm(FormBuilderInterface $builder, ContentTypeInterface $contentType)
    {
        $fields = $this->contentTypeManager->getOrderedFields($contentType);
        $fieldsBuilder = $builder->create('fields', FieldsType::class, [
            'label' => 'content_type.form.fields',
            'translation_domain' => 'AdvancedContentBundle',
        ]);

        foreach ($fields as $field) {
            if ($field->getLayout()) {
                // do not add fields which are not top-level
                continue;
            }
            $this->buildContentTypeFieldForm($fieldsBuilder, $field);
        }
        $builder->add($fieldsBuilder);
        $fieldsBuilder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
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
                        $this->translator->trans('field_type.errors.duplicated_slug_detail', ['%slug%' => $slug], 'AdvancedContentBundle')
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
                        'type_choices' => $this->fieldManager->getFieldTypeFormChoices(),
                        'data_class'   => $this->configurationManager->getEntityClass('field'),
                    ]);
                }
            }
            $event->setData($data);
        });
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
            ->addModelTransformer(new FieldsTransformer($contentType))
        ;
    }

    /**
     * Add given field on content type form
     *
     * @param FormBuilderInterface $formBuilder
     * @param FieldInterface       $field
     */
    public function buildContentTypeFieldForm(FormBuilderInterface $formBuilder, FieldInterface $field)
    {
        $this->buildNamedContentTypeFieldForm($formBuilder, $field, $field->getSlug() ?? $field->getName());
    }

    /**
     * @param FormBuilderInterface $formBuilder
     * @param FieldInterface       $field
     * @param string               $formName
     */
    public function buildNamedContentTypeFieldForm(FormBuilderInterface $formBuilder, FieldInterface $field, $formName)
    {
        $fieldTypeChoices = $this->fieldManager->getFieldTypeFormChoices();
        $formBuilder->add($formName, FieldType::class, [
            'type_choices' => $fieldTypeChoices,
            'data_class'   => $this->configurationManager->getEntityClass('field'),
            'data'         => $field,
        ]);
    }

    /**
     * Build form for Content Type create
     *
     * @param FormBuilderInterface $builder
     */
    public function buildCreateContentTypeForm(FormBuilderInterface $builder)
    {
        $builder->add('name', TextType::class, ['required' => true]);
    }
}
