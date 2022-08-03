<?php

namespace Sherlockode\AdvancedContentBundle\FieldType;

use Sherlockode\AdvancedContentBundle\Form\DataTransformer\StringToArrayTransformer;
use Sherlockode\AdvancedContentBundle\Form\Type\RepeaterGroupCollectionType;
use Sherlockode\AdvancedContentBundle\Form\Type\RepeaterType;
use Sherlockode\AdvancedContentBundle\Manager\FieldManager;
use Sherlockode\AdvancedContentBundle\Model\FieldInterface;
use Sherlockode\AdvancedContentBundle\Model\FieldValueInterface;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormBuilderInterface;

class Repeater extends AbstractFieldType
{
    /**
     * @var FieldManager
     */
    private $fieldManager;

    /**
     * @param FieldManager $fieldManager
     */
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

    public function buildContentFieldValue(FormBuilderInterface $builder, FieldInterface $field)
    {
        parent::buildContentFieldValue($builder, $field);

        $fields = [];
        $layout = null;
        $childrenLayouts = $field->getChildren();
        if (count($childrenLayouts) > 0) {
            $fields = $childrenLayouts[0]->getChildren();
            $layout = $childrenLayouts[0];
        }
        $builder
            ->add('children', RepeaterGroupCollectionType::class, [
                'entry_options' => [
                    'fields' => $fields,
                    'contentType' => $this->fieldManager->getLayoutFieldContentType($field),
                    'layout' => $layout,
                ],
            ])
        ;
    }

    public function getValueModelTransformer(FieldInterface $field)
    {
        return new StringToArrayTransformer();
    }

    /**
     * Get field's code
     *
     * @return string
     */
    public function getCode()
    {
        return 'repeater';
    }

    /**
     * Add field's options
     *
     * @param Form|FormBuilderInterface $builder
     *
     * @return void
     */
    public function addFieldOptions($builder)
    {
        $builder->add('children', RepeaterType::class);
    }

    /**
     * Repeater cannot be rendered directly, FieldGroupValues should be iterated through
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

    /**
     * @return string
     */
    public function getFieldGroup()
    {
        return 'layout';
    }

    /**
     * @return bool
     */
    public function canHaveChildren()
    {
        return true;
    }
}
