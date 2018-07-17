<?php

namespace Sherlockode\AdvancedContentBundle\FieldType;

use Sherlockode\AdvancedContentBundle\Form\DataTransformer\StringToArrayTransformer;
use Sherlockode\AdvancedContentBundle\Model\FieldInterface;
use Sherlockode\AdvancedContentBundle\Model\FieldValueInterface;
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
     * @param FieldInterface       $field
     *
     * @return void
     */
    public function buildContentFieldValue(FormBuilderInterface $builder, FieldInterface $field)
    {
        parent::buildContentFieldValue($builder, $field);

        $options = [];
        $isRequired = false;
        if ($field->isIsRequired()) {
            $isRequired = true;
        }
        $options['required'] = $isRequired;

        $builder->get('value')
            ->add('href', UrlType::class, $options)
            ->addModelTransformer(new StringToArrayTransformer())
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

        if (empty($value['href'])) {
            return '';
        }

        $options = $this->getFieldOptions($fieldValue->getField());

        $width = '';
        $height = '';
        if (!empty($options['width'])) {
            $width = 'width="' . $options['width'] . '"';
        }
        if (!empty($options['height'])) {
            $height = 'height="' . $options['height'] . '"';
        }

        return '<iframe src="' . $value['href'] . '" ' . $width . ' ' . $height . '></iframe>';
    }
}
