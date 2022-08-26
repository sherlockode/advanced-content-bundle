<?php

namespace Sherlockode\AdvancedContentBundle\FieldType;

use Sherlockode\AdvancedContentBundle\Form\DataTransformer\StringToDateTimeTransformer;
use Sherlockode\AdvancedContentBundle\Manager\ConfigurationManager;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormBuilderInterface;

class Date extends AbstractFieldType
{
    /**
     * @var ConfigurationManager
     */
    private $configurationManager;

    /**
     * @param ConfigurationManager $configurationManager
     */
    public function __construct(ConfigurationManager $configurationManager)
    {
        $this->configurationManager = $configurationManager;
    }

    /**
     * @return string
     */
    public function getFormFieldType()
    {
        return DateTimeType::class;
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
            ->add('time', ChoiceType::class, [
                'required' => false,
                'label' => 'field_type.date.time.label',
                'choices' => [
                    'yes' => 1,
                    'no' => 0,
                ],
                'choice_translation_domain' => 'AdvancedContentBundle',
            ])
        ;
    }

    /**
     * @return array
     */
    public function getFieldOptionNames()
    {
        return ['time'];
    }

    /**
     * Get options to apply on field value
     *
     * @return array
     */
    public function getFormFieldValueOptions()
    {
        $fieldOptions = [];

        if (!isset($fieldOptions['time'])) {
            $fieldOptions['time'] = $this->configurationManager->getDefaultDateIncludeTime();
        }

        $class = 'acb-date';
        $format = 'dd/MM/yyyy';
        if ($fieldOptions['time']) {
            $format .= ' hh:mm:ss';
            $class .= ' datetimepicker';
        }

        $formFieldOptions = [];
        $formFieldOptions['attr'] = ['class' => $class];
        $formFieldOptions['widget'] = 'single_text';
        $formFieldOptions['format'] = $format;
        $formFieldOptions['html5'] = false;

        return $formFieldOptions;
    }

    /**
     * Get model transformer for value field
     *
     * @return DataTransformerInterface
     */
    public function getValueModelTransformer()
    {
        return new StringToDateTimeTransformer();
    }

    /**
     * Get field's code
     *
     * @return string
     */
    public function getCode()
    {
        return 'date';
    }
}
