<?php

namespace Sherlockode\AdvancedContentBundle\FieldType;

use Sherlockode\AdvancedContentBundle\Model\FieldInterface;
use Sherlockode\AdvancedContentBundle\Model\FieldValueInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Form;

interface FieldTypeInterface
{
    /**
     * Get options to apply on field value
     *
     * @param FieldInterface $field
     *
     * @return array
     */
    public function getFormFieldValueOptions(FieldInterface $field);

    /**
     * Get field's options
     *
     * @param FieldInterface $field
     *
     * @return array
     */
    public function getFieldOptions(FieldInterface $field);

    /**
     * Add field value's field(s) to content form
     *
     * @param FormBuilderInterface $builder
     * @param FieldInterface       $field
     *
     * @return void
     */
    public function buildContentFieldValue(FormBuilderInterface $builder, FieldInterface $field);

    /**
     * Add field's options
     *
     * @param Form|FormBuilderInterface $builder
     *
     * @return void
     */
    public function addFieldOptions($builder);

    /**
     * Get field's code
     *
     * @return string
     */
    public function getCode();

    /**
     * Render field value
     *
     * @param FieldValueInterface $fieldValue
     *
     * @return mixed
     */
    public function render(FieldValueInterface $fieldValue);

    /**
     * Cleanup field options (in case of field type change)
     *
     * @param FieldInterface $field
     */
    public function clearOptions(FieldInterface $field);

    /**
     * Get Field option names
     *
     * @return array
     */
    public function getFieldOptionNames();

    /**
     * Get model transformer for value field
     *
     * @param FieldInterface $field
     *
     * @return DataTransformerInterface|null
     */
    public function getValueModelTransformer(FieldInterface $field);

    /**
     * Update fieldValue value before saving it
     *
     * @param FieldValueInterface $fieldValue
     * @param array               $changeSet
     *
     * @return void
     */
    public function updateFieldValueValue(FieldValueInterface $fieldValue, $changeSet);

    /**
     * @return string
     */
    public function getFieldGroup();

    /**
     * @param FieldValueInterface $fieldValue
     *
     * @return mixed
     */
    public function getRawValue(FieldValueInterface $fieldValue);

    /**
     * @return bool
     */
    public function canHaveChildren();
}
