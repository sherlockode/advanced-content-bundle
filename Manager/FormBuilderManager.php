<?php

namespace Sherlockode\AdvancedContentBundle\Manager;

use Sherlockode\AdvancedContentBundle\Form\DataTransformer\FieldsTransformer;
use Sherlockode\AdvancedContentBundle\Form\Type\FieldType;
use Sherlockode\AdvancedContentBundle\Form\Type\FieldValueType;
use Sherlockode\AdvancedContentBundle\Form\DataTransformer\FieldValuesTransformer;
use Sherlockode\AdvancedContentBundle\Model\ContentInterface;
use Sherlockode\AdvancedContentBundle\Model\ContentTypeInterface;
use Sherlockode\AdvancedContentBundle\Model\FieldInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\FormType;

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
     * FormBuilderManager constructor.
     *
     * @param ContentManager       $contentManager
     * @param FieldManager         $fieldManager
     * @param ConfigurationManager $configurationManager
     * @param ContentTypeManager   $contentTypeManager
     */
    public function __construct(
        ContentManager $contentManager,
        FieldManager $fieldManager,
        ConfigurationManager $configurationManager,
        ContentTypeManager $contentTypeManager
    ) {
        $this->contentManager = $contentManager;
        $this->fieldManager = $fieldManager;
        $this->configurationManager = $configurationManager;
        $this->contentTypeManager = $contentTypeManager;
    }

    /**
     * Build custom form for Content edit
     *
     * @param FormBuilderInterface $builder
     * @param ContentInterface     $content
     */
    public function buildContentForm(FormBuilderInterface $builder, ContentInterface $content)
    {
        $fields = $this->contentTypeManager->getOrderedFields($content->getContentType());

        $fieldsBuilder = $builder->create('fieldValues', FormType::class);
        foreach ($fields as $field) {
            $fieldsBuilder->add($field->getSlug(), FieldValueType::class, [
                'label'      => $field->getName(),
                'required'   => $field->isIsRequired(),
                'field_type' => $this->fieldManager->getFieldType($field),
                'field'      => $field,
                'data_class' => $this->configurationManager->getEntityClass('field_value'),
            ]);
        }
        $builder->add($fieldsBuilder);
        $fieldsBuilder
            ->addViewTransformer(new FieldValuesTransformer($this->contentManager, $content));
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
        $fieldsBuilder = $builder->create('fields', FormType::class);

        foreach ($fields as $field) {
            $this->buildContentTypeFieldForm($fieldsBuilder, $field);
        }
        $builder->add($fieldsBuilder);
        $fieldsBuilder
            ->addViewTransformer(new FieldsTransformer($contentType))
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
        $fieldTypeChoices = $this->fieldManager->getFieldTypeFormChoices();
        $formBuilder->add($field->getSlug(), FieldType::class, [
            'label'        => $field->getName(),
            'field_type'   => $this->fieldManager->getFieldType($field),
            'field'        => $field,
            'type_choices' => $fieldTypeChoices,
            'data_class'   => $this->configurationManager->getEntityClass('field'),
            'data'         => $field,
            'field_manager' => $this->fieldManager,
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

    /**
     * Add new field on content type form
     *
     * @param FormBuilderInterface $builder
     * @param FieldInterface       $field
     */
    public function buildSingleContentTypeFieldForm(FormBuilderInterface $builder, FieldInterface $field)
    {
        $fieldsBuilder = $builder->create('fields', FormType::class, ['mapped' => false]);
        $this->buildContentTypeFieldForm($fieldsBuilder, $field);
        $builder->add($fieldsBuilder);
    }
}
