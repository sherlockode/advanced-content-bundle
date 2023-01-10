<?php

namespace Sherlockode\AdvancedContentBundle\Manager;

use Sherlockode\AdvancedContentBundle\Element\ElementInterface;
use Sherlockode\AdvancedContentBundle\Exception\InvalidElementException;
use Sherlockode\AdvancedContentBundle\FieldType\FieldTypeInterface;
use Sherlockode\AdvancedContentBundle\LayoutType\LayoutTypeInterface;

class ElementManager
{
    /**
     * @var ElementInterface[]
     */
    private $elements;

    /**
     * @var array
     */
    private $fieldsConfiguration;

    public function __construct(array $fieldsConfiguration)
    {
        $this->elements = [];
        $this->fieldsConfiguration = $fieldsConfiguration;
    }

    /**
     * Add field type
     *
     * @param FieldTypeInterface $fieldType
     */
    public function addFieldType(FieldTypeInterface $fieldType)
    {
        $enabled = true;
        if (isset($this->fieldsConfiguration[$fieldType->getCode()])) {
            if ($this->fieldsConfiguration[$fieldType->getCode()]['enabled'] === false) {
                $enabled = false;
            }
            $fieldType->setConfigData($this->fieldsConfiguration[$fieldType->getCode()]);
        }
        if ($enabled) {
            $this->elements[$fieldType->getCode()] = $fieldType;
        }
    }

    /**
     * Get available field types
     *
     * @return array
     */
    public function getGroupedFieldTypes()
    {
        $choices = [];
        foreach ($this->elements as $element) {
            if ($element instanceof FieldTypeInterface) {
                $fieldGroup = 'field_type.group.' . $element->getFieldGroup();
                if (!isset($choices[$fieldGroup])) {
                    $choices[$fieldGroup] = [];
                }
                $choices[$fieldGroup][$element->getFormFieldLabel()] = $element;
            }
        }

        return $choices;
    }

    /**
     * Add layout type
     *
     * @param LayoutTypeInterface $layoutType
     */
    public function addLayoutType(LayoutTypeInterface $layoutType)
    {
        $this->elements[$layoutType->getCode()] = $layoutType;
    }

    /**
     * Get element
     *
     * @param string $elementCode
     *
     * @return ElementInterface
     *
     * @throws InvalidElementException
     */
    public function getElementByCode($elementCode)
    {
        if (!isset($this->elements[$elementCode])) {
            throw new InvalidElementException(sprintf('Element "%s" is not handled.', $elementCode));
        }
        return $this->elements[$elementCode];
    }
}
