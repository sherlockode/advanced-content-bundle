<?php

namespace Sherlockode\AdvancedContentBundle\Manager;

use Sherlockode\AdvancedContentBundle\Exception\InvalidFieldTypeException;
use Sherlockode\AdvancedContentBundle\FieldType\Checkbox;
use Sherlockode\AdvancedContentBundle\FieldType\FieldTypeInterface;
use Sherlockode\AdvancedContentBundle\FieldType\Link;
use Sherlockode\AdvancedContentBundle\FieldType\Radio;
use Sherlockode\AdvancedContentBundle\FieldType\Text;
use Sherlockode\AdvancedContentBundle\FieldType\TextArea;
use Sherlockode\AdvancedContentBundle\FieldType\Wysiwyg;
use Sherlockode\AdvancedContentBundle\Model\FieldInterface;

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
            $choices[$fieldType->getLabel()] = $code;
        }

        return $choices;
    }

    /**
     * Get field type
     *
     * @param FieldInterface $field
     *
     * @return FieldTypeInterface
     *
     * @throws InvalidFieldTypeException
     */
    public function getFieldType(FieldInterface $field)
    {
        if (!isset($this->fieldTypes[$field->getType()])) {
            throw new InvalidFieldTypeException(sprintf("Field type %s is not handled.", $field->getType()));
        }
        return $this->fieldTypes[$field->getType()];
    }
}
