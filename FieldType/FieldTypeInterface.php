<?php

namespace Sherlockode\AdvancedContentBundle\FieldType;

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
     * Get options to apply on element
     *
     * @return array
     */
    public function getFormElementOptions();

    /**
     * @return string
     */
    public function getFrontTemplate();

    /**
     * @return mixed
     */
    public function getPreviewTemplate();

    /**
     * Add element's field(s) to content form
     *
     * @param FormBuilderInterface $builder
     *
     * @return void
     */
    public function buildContentElement(FormBuilderInterface $builder);

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
     * @param mixed $element
     *
     * @return mixed
     */
    public function getRawValue($element);

    /**
     * @param array $data
     *
     * @return $this
     */
    public function setConfigData(array $data);
}
