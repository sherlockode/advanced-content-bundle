<?php

namespace Sherlockode\AdvancedContentBundle\FieldType;

use Sherlockode\AdvancedContentBundle\Model\FieldValueInterface;

abstract class AbstractFieldType implements FieldTypeInterface
{
    /**
     * Get FieldValue value
     *
     * @param FieldValueInterface $fieldValue
     *
     * @return string
     */
    public function getValue(FieldValueInterface $fieldValue)
    {
        return $fieldValue->getValue();
    }
}
