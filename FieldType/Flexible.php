<?php

namespace Sherlockode\AdvancedContentBundle\FieldType;

use Sherlockode\AdvancedContentBundle\Form\DataTransformer\StringToArrayTransformer;
use Sherlockode\AdvancedContentBundle\Form\Type\FlexibleGroupCollectionType;
use Sherlockode\AdvancedContentBundle\Form\Type\FlexibleType;
use Sherlockode\AdvancedContentBundle\Manager\FieldManager;
use Sherlockode\AdvancedContentBundle\Model\FieldValueInterface;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormBuilderInterface;

class Flexible extends AbstractFieldType
{
    /**
     * @var FieldManager
     */
    private $fieldManager;

    public function __construct(FieldManager $fieldManager)
    {
        $this->fieldManager = $fieldManager;
    }

    /**
     * @return string
     */
    public function getFormFieldType()
    {
        return FormType::class;
    }

    public function buildContentFieldValue(FormBuilderInterface $builder)
    {
        parent::buildContentFieldValue($builder);

        $builder
            ->add('children', FlexibleGroupCollectionType::class, [
                'contentType' => $this->fieldManager->getLayoutFieldContentType($field),
                'layouts' => $field->getChildren(),
            ])
        ;
    }

    public function getValueModelTransformer()
    {
        return new StringToArrayTransformer();
    }

    public function addFieldOptions($builder)
    {
        $builder->add('children', FlexibleType::class);
    }

    /**
     * Get field's code
     *
     * @return string
     */
    public function getCode()
    {
        return 'flexible';
    }

    /**
     * @return string
     */
    public function getFieldGroup()
    {
        return 'layout';
    }

    /**
     * Flexible cannot be rendered directly, FieldGroupValues should be iterated through
     *
     * @param FieldValueInterface $fieldValue
     *
     * @return mixed|string
     */
    public function render(FieldValueInterface $fieldValue)
    {
        return '';
    }

    /**
     * @param FieldValueInterface $fieldValue
     *
     * @return mixed
     */
    public function getRawValue(FieldValueInterface $fieldValue)
    {
        return '';
    }
}
