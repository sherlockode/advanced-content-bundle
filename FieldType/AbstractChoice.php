<?php

namespace Sherlockode\AdvancedContentBundle\FieldType;

use Sherlockode\AdvancedContentBundle\Form\DataTransformer\StringToArrayTransformer;
use Sherlockode\AdvancedContentBundle\Form\DataTransformer\SerializedStringToStringTransformer;
use Sherlockode\AdvancedContentBundle\Model\FieldInterface;
use Sherlockode\AdvancedContentBundle\Model\FieldValueInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Form;

abstract class AbstractChoice extends AbstractFieldType implements FieldValidationInterface
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
        $value = unserialize($fieldValue->getValue());

        $values = [];
        foreach ($value as $valueId) {
            if (!empty($options[$valueId])) {
                $values[] = $options[$valueId];
            }
        }

        return implode(',', $values);
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
        if ($this->getIsMultipleChoice($field)) {
            return new StringToArrayTransformer();
        }

        return new SerializedStringToStringTransformer();
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
     * Get Field option names
     *
     * @return array
     */
    public function getFieldOptionNames()
    {
        return ['choices'];
    }

    /**
     * @return string
     */
    public function getFieldGroup()
    {
        return 'choice';
    }

    /**
     * @param array $data
     *
     * @return array
     */
    public function validate($data)
    {
        if ($data['required']) {
            if (!isset($data['options']['choices']) ||
                !is_array($data['options']['choices']) ||
                count($data['options']['choices']) == 0
            ) {
                return ['empty_collection_field'];
            }
        }

        return [];
    }
}
