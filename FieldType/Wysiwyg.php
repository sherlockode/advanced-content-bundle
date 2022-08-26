<?php

namespace Sherlockode\AdvancedContentBundle\FieldType;

use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Sherlockode\AdvancedContentBundle\Manager\ConfigurationManager;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Form;

class Wysiwyg extends AbstractFieldType
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
     * Get options to apply on field value
     *
     * @return array
     */
    public function getFormFieldValueOptions()
    {
        $fieldOptions = [];

        if (!isset($fieldOptions['toolbar'])) {
            $fieldOptions['toolbar'] = $this->configurationManager->getDefaultWysiwygToolbar();
        }

        $formFieldOptions = ['config' => ['toolbar' => $fieldOptions['toolbar']]];

        return $formFieldOptions;
    }

    /**
     * @return string
     */
    public function getFormFieldType()
    {
        return CKEditorType::class;
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
            ->add('toolbar', ChoiceType::class, [
                'label' => 'field_type.wysiwyg.toolbar.label',
                'choices' => [
                    'field_type.wysiwyg.toolbar.basic' => 'basic',
                    'field_type.wysiwyg.toolbar.standard' => 'standard',
                    'field_type.wysiwyg.toolbar.full' => 'full'
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
        return ['toolbar'];
    }

    /**
     * Get field's code
     *
     * @return string
     */
    public function getCode()
    {
        return 'wysiwyg';
    }
}
