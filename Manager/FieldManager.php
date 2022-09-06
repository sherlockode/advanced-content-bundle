<?php

namespace Sherlockode\AdvancedContentBundle\Manager;

use Sherlockode\AdvancedContentBundle\Exception\InvalidFieldTypeException;
use Sherlockode\AdvancedContentBundle\FieldType\FieldTypeInterface;

class FieldManager
{
    /**
     * @var FieldTypeInterface[]
     */
    private $fieldTypes;

    public function __construct()
    {
        $this->fieldTypes = [];
    }

    /**
     * Add field type
     *
     * @param FieldTypeInterface $fieldType
     */
    public function addFieldType(FieldTypeInterface $fieldType)
    {
        $this->fieldTypes[$fieldType->getCode()] = $fieldType;
    }

    /**
     * Get available field types
     *
     * @return array
     */
    public function getFieldTypeFormChoices()
    {
        $choices = [];
        foreach ($this->fieldTypes as $code => $fieldType) {
            $fieldGroup = 'field_type.group.' . $fieldType->getFieldGroup();
            if (!isset($choices[$fieldGroup])) {
                $choices[$fieldGroup] = [];
            }
            $choices[$fieldGroup]['field_type.' . $fieldType->getCode() . '.label'] = $code;
        }

        return $choices;
    }

    /**
     * Get field type
     *
     * @param string $fieldTypeCode
     *
     * @return FieldTypeInterface
     *
     * @throws InvalidFieldTypeException
     */
    public function getFieldTypeByCode($fieldTypeCode)
    {
        if (!isset($this->fieldTypes[$fieldTypeCode])) {
            throw new InvalidFieldTypeException(sprintf('Field type "%s" is not handled.', $fieldTypeCode));
        }
        return $this->fieldTypes[$fieldTypeCode];
    }
}
