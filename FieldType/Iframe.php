<?php

namespace Sherlockode\AdvancedContentBundle\FieldType;

use Sherlockode\AdvancedContentBundle\Form\DataTransformer\StringToArrayTransformer;
use Sherlockode\AdvancedContentBundle\Model\FieldValueInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormBuilderInterface;

class Iframe extends AbstractFieldType
{
    /**
     * @return string
     */
    public function getFormFieldType()
    {
        return FormType::class;
    }

    /**
     * Add field value's field(s) to content form
     *
     * @param FormBuilderInterface $builder
     *
     * @return void
     */
    public function buildContentFieldValue(FormBuilderInterface $builder)
    {
        parent::buildContentFieldValue($builder);

        $builder->get('value')
            ->add('href', UrlType::class)
        ;
    }

    /**
     * Get model transformer for value field
     *
     * @return DataTransformerInterface
     */
    public function getValueModelTransformer()
    {
        return new StringToArrayTransformer();
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
        $builder->get('options')
            ->add('width', NumberType::class, ['required' => false])
            ->add('height', NumberType::class, ['required' => false])
        ;
    }

    public function getFieldOptionNames()
    {
        return ['width', 'height'];
    }

    /**
     * Get field's code
     *
     * @return string
     */
    public function getCode()
    {
        return 'iframe';
    }

    /**
     * @param FieldValueInterface $fieldValue
     *
     * @return mixed
     */
    public function getRawValue(FieldValueInterface $fieldValue)
    {
        return unserialize($fieldValue->getValue());
    }
}
