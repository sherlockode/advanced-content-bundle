<?php

namespace Sherlockode\AdvancedContentBundle\FieldType;

use Sherlockode\AdvancedContentBundle\Model\FieldInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Form;

abstract class AbstractChoice extends AbstractFieldType
{
    /**
     * @var bool
     */
    protected $isMultipleChoice;

    /**
     * Get available options for given field type
     *
     * @return array
     */
    public function getFieldTypeOptions()
    {
        return [
            'choices' => [
                'label' => 'Choices',
                'type'  => 'choices'
            ],
        ];
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
        $formFieldOptions = [];
        $formFieldOptions['choices'] = array_flip($this->getFieldOptionsArray($field));
        $formFieldOptions['expanded'] = true;
        $formFieldOptions['multiple'] = $this->isMultipleChoice;

        return $formFieldOptions;
    }

    /**
     * Get field's options
     *
     * @param FieldInterface $field
     *
     * @return array
     */
    protected function getFieldOptionsArray(FieldInterface $field)
    {
        $choices = [];
        $fieldOptions = $this->getFieldOptions($field);
        if (isset($fieldOptions['choices']) && is_array($fieldOptions['choices'])) {
            $choices = $fieldOptions['choices'];
        }
        return $choices;
    }

    /**
     * @return string
     */
    public function getFormFieldType()
    {
        return ChoiceType::class;
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
            ->add('choices', CollectionType::class, [
                'entry_type' => TextType::class,
                'entry_options' => ['attr' => ['class' => 'form-control choice-label']],
                'allow_add' => true,
                'allow_delete' => true,
                'attr' => ['class' => 'choices'],
            ])
        ;
    }
}
