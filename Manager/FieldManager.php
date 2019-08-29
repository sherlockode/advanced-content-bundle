<?php

namespace Sherlockode\AdvancedContentBundle\Manager;

use Sherlockode\AdvancedContentBundle\Exception\InvalidFieldTypeException;
use Sherlockode\AdvancedContentBundle\FieldType\FieldTypeInterface;
use Sherlockode\AdvancedContentBundle\Model\ContentTypeInterface;
use Sherlockode\AdvancedContentBundle\Model\FieldInterface;
use Sherlockode\AdvancedContentBundle\Model\LayoutInterface;

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
     * @param FieldInterface $field
     *
     * @return FieldTypeInterface
     */
    public function getFieldType(FieldInterface $field)
    {
        return $this->getFieldTypeByCode($field->getType());
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

    /**
     * @param FieldInterface $field
     *
     * @return ContentTypeInterface
     *
     * @throws \LogicException
     */
    public function getLayoutFieldContentType(FieldInterface $field)
    {
        if ($field->getContentType() instanceof ContentTypeInterface) {
            return $field->getContentType();
        }

        if ($field->getLayout() instanceof LayoutInterface) {
            return $this->getLayoutFieldContentType($field->getLayout()->getParent());
        }

        throw new \LogicException(sprintf("Field %d is not linked to any content type", $field->getId()));
    }
}
