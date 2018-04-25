<?php

namespace Sherlockode\AdvancedContentBundle\FieldType;

use Sherlockode\AdvancedContentBundle\Model\FieldInterface;
use Sherlockode\AdvancedContentBundle\Model\FieldValueInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Form;

abstract class AbstractFieldType implements FieldTypeInterface
{
    /**
     * Get field's options
     *
     * @param FieldInterface $field
     *
     * @return array
     */
    public function getFieldOptions(FieldInterface $field)
    {
        return unserialize($field->getOptions());
    }

    /**
     * Add field value's field(s) to content form
     *
     * @param FormBuilderInterface $builder
     * @param FieldInterface       $field
     *
     * @return void
     */
    public function buildContentFieldValue(FormBuilderInterface $builder, FieldInterface $field)
    {
        $builder->add('value', $this->getFormFieldType(), $this->getFormFieldValueOptions($field));
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
     * Get field's label
     *
     * @return string
     */
    public function getLabel()
    {
        return ucfirst($this->getCode());
    }

    /**
     * Render field value
     *
     * @param FieldValueInterface $fieldValue
     *
     * @return mixed
     */
    public function render(FieldValueInterface $fieldValue)
    {
        return $fieldValue->getValue();
    }
}
