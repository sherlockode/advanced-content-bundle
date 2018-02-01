<?php

namespace Sherlockode\AdvancedContentBundle\FieldType;

use Sherlockode\AdvancedContentBundle\Model\FieldValue;

abstract class AbstractFieldType implements FieldTypeInterface
{
    /**
     * Get FieldValue value
     *
     * @param FieldValue $fieldValue
     *
     * @return string
     */
    public function getValue(FieldValue $fieldValue)
    {
        return $fieldValue->getValue();
    }
}
