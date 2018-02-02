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
     * Get available field types
     *
     * @return array
     */
    public function getFieldTypes()
    {
        return [
            'text' => new Text(),
            'textarea' => new TextArea(),
            'checkbox' => new Checkbox(),
            'radio' => new Radio(),
            'wysiwyg' => new Wysiwyg(),
            'link' => new Link(),
        ];
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
        if (!isset($this->getFieldTypes()[$field->getType()])) {
            throw new InvalidFieldTypeException(sprintf("Field type %s is not handled.", $field->getType()));
        }
        return $this->getFieldTypes()[$field->getType()];
    }
}
