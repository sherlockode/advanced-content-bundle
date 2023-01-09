<?php

namespace Sherlockode\AdvancedContentBundle\FieldType;

use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;

abstract class AbstractFieldType implements FieldTypeInterface
{
    protected $configData = [];

    public function setConfigData(array $data)
    {
        $this->configData = $data;

        return $this;
    }

    /**
     * @return string
     */
    public function getFormFieldLabel()
    {
        return 'field_type.' . $this->getCode() . '.label';
    }

    public function getIconClass()
    {
        return $this->configData['icon'] ?? $this->getDefaultIconClass();
    }

    /**
     * @return string
     */
    protected function getDefaultIconClass()
    {
        return 'fa-solid fa-gear';
    }

    /**
     * @return string
     */
    public function getFrontTemplate()
    {
        return '@SherlockodeAdvancedContent/Field/front/' . $this->getCode() . '.html.twig';
    }

    /**
     * @return string
     */
    public function getPreviewTemplate()
    {
        return '@SherlockodeAdvancedContent/Field/preview/'. $this->getCode() .'.html.twig';
    }

    /**
     * Add element's field(s) to content form
     *
     * @param FormBuilderInterface $builder
     *
     * @return void
     */
    public function buildContentElement(FormBuilderInterface $builder)
    {
        $builder->add('fieldType', HiddenType::class);
        $builder->add('position', HiddenType::class);
        $builder->add('value', $this->getFormFieldType(), array_merge(
            $this->getDefaultFormElementOptions(),
            $this->getFormElementOptions()
        ));

        $modelTransformer = $this->getValueModelTransformer();
        if ($modelTransformer !== null) {
            $builder->get('value')
                ->addModelTransformer($modelTransformer);
        }
    }

    /**
     * Get options to apply on element
     *
     * @return array
     */
    public function getFormElementOptions()
    {
        return [];
    }

    /**
     * Get model transformer for value field
     *
     * @return null
     */
    public function getValueModelTransformer()
    {
        return null;
    }

    /**
     * Add field hint to element form
     *
     * @return array
     */
    public function getDefaultFormElementOptions()
    {
        $defaultOptions = ['label' => false];
        if ($this->getHint()) {
            $defaultOptions['attr']['help'] = $this->getHint();
        }
        return $defaultOptions;
    }

    public function getHint()
    {
        return null;
    }

    /**
     * @return string
     */
    public function getFieldGroup()
    {
        return 'other';
    }

    /**
     * @param mixed $element
     *
     * @return mixed
     */
    public function getRawValue($element)
    {
        return $element;
    }

    /**
     * Get form field type
     *
     * @return string
     */
    abstract public function getFormFieldType();
}
