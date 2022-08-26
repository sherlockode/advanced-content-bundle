<?php

namespace Sherlockode\AdvancedContentBundle\FieldType;

use Sherlockode\AdvancedContentBundle\Model\FieldInterface;
use Sherlockode\AdvancedContentBundle\Model\FieldValueInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormBuilderInterface;

class Boolean extends AbstractChoice
{
    /**
     * @var bool
     */
    protected $isMultipleChoice = false;

    /**
     * @var bool
     */
    protected $isExpanded = false;

    /**
     * Get field's code
     *
     * @return string
     */
    public function getCode()
    {
        return 'boolean';
    }

    /**
     * Get field's options
     *
     * @param FieldInterface $field
     *
     * @return array
     */
    public function getFieldOptionsArray(FieldInterface $field)
    {
        return [
            0 => 'No',
            1 => 'Yes',
        ];
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
    }

    /**
     * Get model transformer for value field
     *
     * @return null
     */
    public function getValueModelTransformer()
    {
        return null;
    }

    /**
     * @param FieldValueInterface $fieldValue
     *
     * @return mixed
     */
    public function getRawValue(FieldValueInterface $fieldValue)
    {
        return (bool) $fieldValue->getValue();
    }
}
