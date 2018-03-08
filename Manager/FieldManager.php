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
            'text' => ['label' => 'Text', 'class' => new Text()],
            'textarea' => ['label' => 'Text Area', 'class' => new TextArea()],
            'checkbox' => ['label' => 'Checkbox', 'class' => new Checkbox()],
            'radio' => ['label' => 'Radio', 'class' => new Radio()],
            'wysiwyg' => ['label' => 'Wysiwyg', 'class' => new Wysiwyg()],
            'link' => ['label' => 'Link', 'class' => new Link()],
        ];
    }

    public function getFieldTypeFormChoices()
    {
        $choices = [];
        foreach ($this->getFieldTypes() as $code => $detail) {
            $choices[$detail['label']] = $code;
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
        if (!isset($this->getFieldTypes()[$field->getType()])) {
            throw new InvalidFieldTypeException(sprintf("Field type %s is not handled.", $field->getType()));
        }
        return $this->getFieldTypes()[$field->getType()]['class'];
    }
}
