<?php

namespace Sherlockode\AdvancedContentBundle\FieldType;

use Sherlockode\AdvancedContentBundle\Form\DataTransformer\StringToArrayTransformer;
use Sherlockode\AdvancedContentBundle\Model\FieldInterface;
use Sherlockode\AdvancedContentBundle\Model\FieldValueInterface;
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
     * @var bool
     */
    protected $isExpanded;

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
        $formFieldOptions['expanded'] = $this->isExpanded;
        $formFieldOptions['multiple'] = $this->getIsMultipleChoice($field);

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

    /**
     * Render field value
     *
     * @param FieldValueInterface $fieldValue
     *
     * @return mixed
     */
    public function render(FieldValueInterface $fieldValue)
    {
        $options = $this->getFieldOptionsArray($fieldValue->getField());
        $value = $this->getFieldValueValue($fieldValue);
        if (is_array($value)) {
            return $this->renderMultipleValue($value, $options);
        }

        return $this->renderSingleValue($value, $options);
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

        if ($this->getIsMultipleChoice($field)) {
            $builder->get('value')
                ->addModelTransformer(new StringToArrayTransformer());
        }
    }

    /**
     * Check if field accept several choices
     *
     * @param FieldInterface $field
     *
     * @return bool
     */
    protected function getIsMultipleChoice(FieldInterface $field)
    {
        $fieldOptions = $this->getFieldOptions($field);
        if (isset($fieldOptions['is_multiple'])) {
            return $fieldOptions['is_multiple'];
        }

        return $this->isMultipleChoice;
    }

    /**
     * Get value of fieldValue
     *
     * @param FieldValueInterface $fieldValue
     *
     * @return string|array
     */
    protected function getFieldValueValue(FieldValueInterface $fieldValue)
    {
        if ($this->getIsMultipleChoice($fieldValue->getField())) {
            return unserialize($fieldValue->getValue());
        }

        return $fieldValue->getValue();
    }

    /**
     * Render values for multiple choices field type
     *
     * @param array $value
     * @param array $options
     *
     * @return string
     */
    protected function renderMultipleValue($value, $options)
    {
        $values = [];
        foreach ($value as $valueId) {
            if (!empty($options[$valueId])) {
                $values[] = $options[$valueId];
            }
        }

        return implode(',', $values);
    }

    /**
     * Render value for single choice field type
     *
     * @param string $value
     * @param array  $options
     *
     * @return string
     */
    protected function renderSingleValue($value, $options)
    {
        if (!empty($options[$value])) {
            return $options[$value];
        }

        return '';
    }

    /**
     * Get Field option names
     *
     * @return array
     */
    public function getFieldOptionNames()
    {
        return ['choices'];
    }
}
