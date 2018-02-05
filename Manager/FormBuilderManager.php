<?php

namespace Sherlockode\AdvancedContentBundle\Manager;

use Sherlockode\AdvancedContentBundle\Form\DataTransformer\FieldsTransformer;
use Sherlockode\AdvancedContentBundle\Form\Type\FieldType;
use Sherlockode\AdvancedContentBundle\Form\Type\FieldValueType;
use Sherlockode\AdvancedContentBundle\Form\DataTransformer\FieldValuesTransformer;
use Sherlockode\AdvancedContentBundle\Model\ContentInterface;
use Sherlockode\AdvancedContentBundle\Model\ContentTypeInterface;
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
     * FormBuilderManager constructor.
     *
     * @param ContentManager       $contentManager
     * @param FieldManager         $fieldManager
     * @param ConfigurationManager $configurationManager
     */
    public function __construct(
        ContentManager $contentManager,
        FieldManager $fieldManager,
        ConfigurationManager $configurationManager
    ) {
        $this->contentManager = $contentManager;
        $this->fieldManager = $fieldManager;
        $this->configurationManager = $configurationManager;
    }

    /**
     * Build custom form for Content edit
     *
     * @param FormBuilderInterface $builder
     * @param ContentInterface     $content
     */
    public function buildContentForm(FormBuilderInterface $builder, ContentInterface $content)
    {
        $fields = $content->getContentType()->getFields();

        $fieldsBuilder = $builder->create('fieldValues', FormType::class);
        foreach ($fields as $field) {
            $fieldsBuilder->add($field->getSlug(), FieldValueType::class, [
                'label' => $field->getName(),
                'required' => $field->isIsRequired(),
                'field_type' => $this->fieldManager->getFieldType($field),
                'field' => $field,
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
                'class' => $this->configurationManager->getEntityClass('content_type'),
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
        $fieldTypeChoices = $this->fieldManager->getFieldTypeFormChoices();
        $fields = $contentType->getFields();
        $fieldsBuilder = $builder->create('fields', FormType::class);
        foreach ($fields as $field) {
            $fieldsBuilder->add($field->getSlug(), FieldType::class, [
                'label' => $field->getName(),
                'field_type' => $this->fieldManager->getFieldType($field),
                'field' => $field,
                'type_choices' => $fieldTypeChoices,
                'data_class' => $this->configurationManager->getEntityClass('field'),
            ]);
        }
        $builder->add($fieldsBuilder);
        $fieldsBuilder
            ->addViewTransformer(new FieldsTransformer($contentType));
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
