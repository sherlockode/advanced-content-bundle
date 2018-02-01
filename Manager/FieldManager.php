<?php

namespace Sherlockode\AdvancedContentBundle\Manager;

use Sherlockode\AdvancedContentBundle\FieldType\Text;
use Sherlockode\AdvancedContentBundle\Model\FieldInterface;

class FieldManager
{
    /**
     * Get available field types
     *
     * @return array
     */
    public function getFieldTypes()
    {
        return [
            'text' => new Text(),
        ];
    }

    /**
     * Get specific options to add on form
     *
     * @param FieldInterface $field
     *
     * @return array
     */
    public function getFieldOptions(FieldInterface $field)
    {
        if (!isset($this->getFieldTypes()[$field->getType()])) {
            return [];
        }

        if (!$field->getOptions()) {
            return [];
        }

        return $this->getFieldTypes()[$field->getType()]->getFormFieldValueOptions($field);
    }
}
