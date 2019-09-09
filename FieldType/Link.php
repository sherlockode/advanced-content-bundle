<?php

namespace Sherlockode\AdvancedContentBundle\FieldType;

use Sherlockode\AdvancedContentBundle\Form\DataTransformer\StringToArrayTransformer;
use Sherlockode\AdvancedContentBundle\Model\FieldInterface;
use Sherlockode\AdvancedContentBundle\Model\FieldValueInterface;
use Symfony\Component\Form\DataTransformerInterface;
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
            ->add('url', UrlType::class)
            ->add('title', TextType::class)
        ;
    }

    /**
     * Get model transformer for value field
     *
     * @param FieldInterface $field
     *
     * @return DataTransformerInterface
     */
    public function getValueModelTransformer(FieldInterface $field)
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

        if (empty($value['url'])) {
            return '';
        }

        $options = $this->getFieldOptions($fieldValue->getField());
        $target = $options['target'];

        return '<a href="' . $value['url'] . '" target="' . $target . '">' . $value['title']. '</a>';
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
