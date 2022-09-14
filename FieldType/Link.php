<?php

namespace Sherlockode\AdvancedContentBundle\FieldType;

use Sherlockode\AdvancedContentBundle\Model\FieldValueInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Form;

class Link extends AbstractFieldType
{
    /**
     * @return string
     */
    public function getFormFieldType()
    {
        return FormType::class;
    }

    public function getIconClass()
    {
        return 'glyphicon glyphicon-link';
    }

    public function getFrontTemplate()
    {
        return '@SherlockodeAdvancedContent/Field/front/link.html.twig';
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
            ->add('url', $this->getUrlFormType())
            ->add('title', TextType::class)
        ;
    }

    /**
     * @return string
     */
    protected function getUrlFormType()
    {
        return UrlType::class;
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
            ->add('target', ChoiceType::class, [
                'choices' => [
                    'Blank' => '_blank',
                    'Self' => '_self'
                ]
            ])
        ;
    }

    /**
     * Get Field option names
     *
     * @return array
     */
    public function getFieldOptionNames()
    {
        return ['target'];
    }

    /**
     * Get field's code
     *
     * @return string
     */
    public function getCode()
    {
        return 'link';
    }

    /**
     * @param FieldValueInterface $fieldValue
     *
     * @return mixed
     */
    public function getRawValue(FieldValueInterface $fieldValue)
    {
        $rawValue = $fieldValue->getValue();
        $url = $this->getUrlValue($rawValue);

        if (!$url) {
            return null;
        }

        $rawValue['url'] = $url;

        return $rawValue;
    }

    /**
     * @param array $value
     *
     * @return string
     */
    protected function getUrlValue($value)
    {
        return $value['url'] ?? '';
    }
}
