<?php

namespace Sherlockode\AdvancedContentBundle\FieldType;

use Sherlockode\AdvancedContentBundle\Form\Type\RepeaterGroupCollectionType;
use Sherlockode\AdvancedContentBundle\Form\Type\RepeaterType;
use Sherlockode\AdvancedContentBundle\Manager\FieldManager;
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

    public function buildContentFieldValue(FormBuilderInterface $builder)
    {
        parent::buildContentFieldValue($builder);

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
}
