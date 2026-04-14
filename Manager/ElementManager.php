<?php

declare(strict_types=1);

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
    private array $elements = [];

    public function __construct(private array $fieldsConfiguration)
    {
    }

    /**
     * Add field type.
     */
    public function addFieldType(FieldTypeInterface $fieldType): void
    {
        $enabled = true;
        if (isset($this->fieldsConfiguration[$fieldType->getCode()])) {
            if (false === $this->fieldsConfiguration[$fieldType->getCode()]['enabled']) {
                $enabled = false;
            }

            $fieldType->setConfigData($this->fieldsConfiguration[$fieldType->getCode()]);
        }

        if ($enabled) {
            $this->elements[$fieldType->getCode()] = $fieldType;
        }
    }

    /**
     * Get available field types.
     */
    public function getGroupedFieldTypes(): array
    {
        $choices = [];
        foreach ($this->elements as $element) {
            if ($element instanceof FieldTypeInterface) {
                $fieldGroup = 'field_type.group.'.$element->getFieldGroup();
                if (!isset($choices[$fieldGroup])) {
                    $choices[$fieldGroup] = [];
                }

                $choices[$fieldGroup][$element->getFormFieldLabel()] = $element;
            }
        }

        return $choices;
    }

    /**
     * Add layout type.
     */
    public function addLayoutType(LayoutTypeInterface $layoutType): void
    {
        $this->elements[$layoutType->getCode()] = $layoutType;
    }

    /**
     * Get element.
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
