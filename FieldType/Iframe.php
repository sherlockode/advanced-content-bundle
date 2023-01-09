<?php

namespace Sherlockode\AdvancedContentBundle\FieldType;

use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
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
     * Add element's field(s) to content form
     *
     * @param FormBuilderInterface $builder
     *
     * @return void
     */
    public function buildContentElement(FormBuilderInterface $builder)
    {
        parent::buildContentElement($builder);

        $builder->get('value')
            ->add('src', UrlType::class, ['label' => 'field_type.iframe.src'])
            ->add('width', IntegerType::class, ['required' => false, 'label' => 'field_type.iframe.width'])
            ->add('height', IntegerType::class, ['required' => false, 'label' => 'field_type.iframe.height'])
        ;
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
