<?php

namespace Sherlockode\AdvancedContentBundle\FieldType;

use Sherlockode\AdvancedContentBundle\Model\FieldValueInterface;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;

class Link extends AbstractFieldType
{
    /**
     * @return string
     */
    public function getFormFieldType()
    {
        return FormType::class;
    }

    protected function getDefaultIconClass()
    {
        return 'fa-solid fa-link';
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
            ->add('url', $this->getUrlFormType(), ['label' => 'field_type.link.url'])
            ->add('title', TextType::class, ['label' => 'field_type.link.title'])
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
