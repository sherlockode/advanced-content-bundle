<?php

namespace Sherlockode\AdvancedContentBundle\FieldType;

use Sherlockode\AdvancedContentBundle\Model\Field;
use Sherlockode\AdvancedContentBundle\Model\FieldValue;

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
     * @param Field $field
     *
     * @return array
     */
    public function getFormFieldValueOptions(Field $field);

    /**
     * Get FieldValue value
     *
     * @param FieldValue $fieldValue
     *
     * @return mixed
     */
    public function getValue(FieldValue $fieldValue);
}
