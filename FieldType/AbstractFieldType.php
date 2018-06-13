<?php

namespace Sherlockode\AdvancedContentBundle\FieldType;

use Sherlockode\AdvancedContentBundle\Model\FieldInterface;
use Sherlockode\AdvancedContentBundle\Model\FieldValueInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Form;

abstract class AbstractFieldType implements FieldTypeInterface
{
    /**
     * Get field's options
     *
     * @param FieldInterface $field
     *
     * @return array
     */
    public function getFieldOptions(FieldInterface $field)
    {
        return unserialize($field->getOptions());
    }

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
        $builder->add('value', $this->getFormFieldType(), array_merge(
            $this->getDefaultFormFieldValueOptions($field),
            $this->getFormFieldValueOptions($field)
        ));
    }

    /**
     * Add field's options
     *
     * @param Form|FormBuilderInterface $builder
     *
     * @return void
     */
    public function addFieldOptions($builder)
    {
    }

    /**
     * Cleanup field options (in case of field type change)
     *
     * @param FieldInterface $field
     */
    public function clearOptions(FieldInterface $field)
    {
        $options = $field->getOptions();
        $options = unserialize($options);

        $optionNames = $this->getFieldOptionNames();
        foreach ($options as $key => $value) {
            if (in_array($key, $optionNames)) {
                continue;
            }
            unset($options[$key]);
        }

        $field->setOptions(serialize($options));
    }

    /**
     * Get Field option names
     *
     * @return array
     */
    public function getFieldOptionNames()
    {
        return [];
    }

    /**
     * Get options to apply on field value
     *
     * @param FieldInterface $field
     *
     * @return array
     */
    public function getFormFieldValueOptions(FieldInterface $field)
    {
        return [];
    }

    /**
     * Get field's label
     *
     * @return string
     */
    public function getLabel()
    {
        return ucfirst($this->getCode());
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
        return $fieldValue->getValue();
    }

    /**
     * Add field hint to field value form
     *
     * @param $field
     *
     * @return array
     */
    public function getDefaultFormFieldValueOptions(FieldInterface $field)
    {
        $defaultOptions = [];
        if ($field->getHint()) {
            $defaultOptions['attr']['help'] = $field->getHint();
        }
        return $defaultOptions;
    }
}
