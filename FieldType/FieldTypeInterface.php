<?php

namespace Sherlockode\AdvancedContentBundle\FieldType;

use Sherlockode\AdvancedContentBundle\Model\FieldInterface;
use Sherlockode\AdvancedContentBundle\Model\FieldValueInterface;

interface FieldTypeInterface
{
    /**
     * Get available options for given field type
     *
     * @return array
     */
    public function getFieldTypeOptions();

    /**
     * Get options to apply on field value
     *
     * @param FieldInterface $field
     *
     * @return array
     */
    public function getFormFieldValueOptions(FieldInterface $field);

    /**
     * Get FieldValue value
     *
     * @param FieldValueInterface $fieldValue
     *
     * @return mixed
     */
    public function getValue(FieldValueInterface $fieldValue);
}
