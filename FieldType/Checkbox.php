<?php

namespace Sherlockode\AdvancedContentBundle\FieldType;

use Sherlockode\AdvancedContentBundle\Form\DataTransformer\StringToArrayTransformer;
use Sherlockode\AdvancedContentBundle\Model\FieldInterface;
use Sherlockode\AdvancedContentBundle\Model\FieldValueInterface;
use Symfony\Component\Form\FormBuilderInterface;

class Checkbox extends AbstractChoice
{
    /**
     * @var bool
     */
    protected $isMultipleChoice = true;

    /**
     * Add field value's field(s) to content form
     *
     * @param FormBuilderInterface $builder
     * @param FieldInterface       $field
     *
     * @return void
     */
    public function buildContentFieldValue(FormBuilderInterface $builder, FieldInterface $field)
    {
        parent::buildContentFieldValue($builder, $field);

        $builder->get('value')
            ->addModelTransformer(new StringToArrayTransformer());
    }

    /**
     * Get field's code
     *
     * @return string
     */
    public function getCode()
    {
        return 'checkbox';
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
        $value = unserialize($value);

        $values = [];
        $options = $this->getFieldOptionsArray($fieldValue->getField());
        foreach ($value as $valueId) {
            if (!empty($options[$valueId])) {
                $values[] = $options[$valueId];
            }
        }
        // TODO return array of labels
        return implode(',', $values);
    }
}
