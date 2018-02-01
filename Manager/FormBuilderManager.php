<?php

namespace Sherlockode\AdvancedContentBundle\Manager;

use Sherlockode\AdvancedContentBundle\Form\Type\FieldValueType;
use Sherlockode\AdvancedContentBundle\Form\DataTransformer\FieldValuesTransformer;
use Sherlockode\AdvancedContentBundle\Model\ContentInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
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
     * @var string
     */
    private $fieldValueClass;

    /**
     * @var string
     */
    private $contentTypeClass;

    /**
     * ContentManager constructor.
     *
     * @param ContentManager $contentManager
     * @param FieldManager   $fieldManager
     */
    public function __construct(ContentManager $contentManager, FieldManager $fieldManager)
    {
        $this->contentManager = $contentManager;
        $this->fieldManager = $fieldManager;
    }

    /**
     * Set entity class configuration
     *
     * @param $config
     */
    public function setConfig($config)
    {
        $this->fieldValueClass = $config['field_value'];
        $this->contentTypeClass = $config['content_type'];
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
            $fieldOptions = $this->fieldManager->getFieldOptions($field);
            $fieldsBuilder->add($field->getSlug(), FieldValueType::class, array_merge([
                'label' => $field->getName(),
                'required' => $field->isIsRequired(),
                'field_type' => $field->getType(),
                'data_class' => $this->fieldValueClass,
            ], $fieldOptions));
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
                'class' => $this->contentTypeClass,
                'choice_label' => 'name'
            ]
        );
    }
}
