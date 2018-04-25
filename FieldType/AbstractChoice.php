<?php

namespace Sherlockode\AdvancedContentBundle\FieldType;

use Sherlockode\AdvancedContentBundle\Model\FieldInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
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
        if (!empty($fieldOptions['choices'])) {
            $fieldChoices = preg_split("/\r\n|\n|\r/", $fieldOptions['choices']);
            foreach ($fieldChoices as $choice) {
                $choice = explode('::', $choice);
                if (count($choice) != 2) {
                    continue;
                }
                $choices[trim($choice[0])] = trim($choice[1]);
            }
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
            ->add('choices', TextareaType::class)
        ;
    }
}
