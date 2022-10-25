<?php

namespace Sherlockode\AdvancedContentBundle\FieldType;

use Sherlockode\AdvancedContentBundle\Model\FieldValueInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\FormBuilderInterface;

interface FieldTypeInterface
{
    /**
     * @return string
     */
    public function getFormFieldLabel();

    /**
     * @return string
     */
    public function getIconClass();

    /**
     * Get options to apply on field value
     *
     * @return array
     */
    public function getFormFieldValueOptions();

    /**
     * @return string
     */
    public function getFrontTemplate();

    /**
     * @return mixed
     */
    public function getPreviewTemplate();

    /**
     * Add field value's field(s) to content form
     *
     * @param FormBuilderInterface $builder
     *
     * @return void
     */
    public function buildContentFieldValue(FormBuilderInterface $builder);

    /**
     * Get field's code
     *
     * @return string
     */
    public function getCode();

    /**
     * Get model transformer for value field
     *
     * @return DataTransformerInterface|null
     */
    public function getValueModelTransformer();

    /**
     * @return string
     */
    public function getHint();

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
     * @param array $data
     *
     * @return $this
     */
    public function setConfigData(array $data);
}
