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
        return $field->getOptions();
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

        $modelTransformer = $this->getValueModelTransformer($field);
        if ($modelTransformer !== null) {
            $builder->get('value')
                ->addModelTransformer($modelTransformer);
        }
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

        $optionNames = $this->getFieldOptionNames();
        foreach ($options as $key => $value) {
            if (in_array($key, $optionNames)) {
                continue;
            }
            unset($options[$key]);
        }

        $field->setOptions($options);
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
     * Get model transformer for value field
     *
     * @param FieldInterface $field
     *
     * @return null
     */
    public function getValueModelTransformer(FieldInterface $field)
    {
        return null;
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
        $defaultOptions = ['label' => false];
        if ($field->getHint()) {
            $defaultOptions['attr']['help'] = $field->getHint();
        }
        return $defaultOptions;
    }

    /**
     * Update fieldValue value before saving it
     *
     * @param FieldValueInterface $fieldValue
     * @param array               $changeSet
     *
     * @return void
     */
    public function updateFieldValueValue(FieldValueInterface $fieldValue, $changeSet)
    {
    }

    /**
     * Get form field type
     *
     * @return string
     */
    abstract public function getFormFieldType();
}
