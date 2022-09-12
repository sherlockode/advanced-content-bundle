<?php

namespace Sherlockode\AdvancedContentBundle\FieldType;

use Sherlockode\AdvancedContentBundle\Model\FieldValueInterface;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
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
            ->add('src', UrlType::class)
            ->add('width', IntegerType::class, ['required' => false])
            ->add('height', IntegerType::class, ['required' => false])
        ;
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
}
