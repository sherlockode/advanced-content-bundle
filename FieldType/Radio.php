<?php

namespace Sherlockode\AdvancedContentBundle\FieldType;

use Sherlockode\AdvancedContentBundle\Model\FieldValueInterface;

class Radio extends AbstractChoice
{
    /**
     * @var bool
     */
    protected $isMultipleChoice = false;

    /**
     * Get field's code
     *
     * @return string
     */
    public function getCode()
    {
        return 'radio';
    }

    /**
     * Render field value
     *
     * @param FieldValueInterface $fieldValue
     *
     * @return mixed
     */
    public function render(FieldValueInterface $fieldValue)
    {
        $value = $fieldValue->getValue();

        $options = $this->getFieldOptionsArray($fieldValue->getField());
        if (!empty($options[$value])) {
            return $options[$value];
        }

        return '';
    }
}
