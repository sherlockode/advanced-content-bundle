<?php

namespace Sherlockode\AdvancedContentBundle\FieldType;

use Sherlockode\AdvancedContentBundle\Form\DataTransformer\StringToArrayTransformer;
use Sherlockode\AdvancedContentBundle\Model\FieldInterface;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormBuilderInterface;

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
        $builder->add('value', $this->getFormFieldType(), $this->getFormFieldValueOptions($field));
    }

    /**
     * Add field's options field(s) to content type form
     *
     * @param FormBuilderInterface $builder
     * @param FieldInterface       $field
     *
     * @return void
     */
    public function buildContentTypeFieldOptions(FormBuilderInterface $builder, FieldInterface $field)
    {
        $builder->add('options', FormType::class);
        $this->addFieldOptions($builder, $field);
        $builder->get('options')->addModelTransformer(new StringToArrayTransformer());
    }

    /**
     * Add field's options
     *
     * @param FormBuilderInterface $builder
     * @param FieldInterface       $field
     *
     * @return void
     */
    public function addFieldOptions(FormBuilderInterface $builder, FieldInterface $field)
    {
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
}
