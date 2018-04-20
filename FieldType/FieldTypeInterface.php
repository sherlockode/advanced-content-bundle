<?php

namespace Sherlockode\AdvancedContentBundle\FieldType;

use Sherlockode\AdvancedContentBundle\Model\FieldInterface;
use Symfony\Component\Form\FormBuilderInterface;

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
     * Get form field type
     *
     * @return string
     */
    public function getFormFieldType();

    /**
     * Add field's options field(s) to content type form
     *
     * @param FormBuilderInterface $builder
     * @param FieldInterface       $field
     *
     * @return void
     */
    public function buildContentTypeFieldOptions(FormBuilderInterface $builder, FieldInterface $field);

    /**
     * Add field's options
     *
     * @param FormBuilderInterface $builder
     * @param FieldInterface       $field
     *
     * @return void
     */
    public function addFieldOptions(FormBuilderInterface $builder, FieldInterface $field);

    /**
     * Get field's code
     *
     * @return string
     */
    public function getCode();

    /**
     * Get field's label
     *
     * @return string
     */
    public function getLabel();
}
