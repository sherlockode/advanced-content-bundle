<?php

namespace Sherlockode\AdvancedContentBundle\FieldType;

use Sherlockode\AdvancedContentBundle\Element\AbstractElement;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

abstract class AbstractFieldType extends AbstractElement implements FieldTypeInterface
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
        parent::buildContentElement($builder);

        $builder->add('value', $this->getFormFieldType(), array_merge(
            $this->getDefaultFormElementOptions(),
            $this->getFormElementOptions()
        ));

        $modelTransformer = $this->getValueModelTransformer();
        if ($modelTransformer !== null) {
            $builder->get('value')
                ->addModelTransformer($modelTransformer);
        }

        $builder->get('value')->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) {
            $data = $event->getData();
            $form = $event->getForm();
            if ($form->getConfig()->getCompound()) {
                $cleanData = [];
                // remove possibly obsolete data if the form was changed
                foreach ($form as $key => $child) {
                    if (isset($data[$key])) {
                        $cleanData[$key] = $data[$key];
                    }
                }

                $event->setData($cleanData);
            }
        });
    }

    /**
     * @return array
     */
    public function getDefaultFormElementOptions()
    {
        return ['label' => false];
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
     * Get options to apply on element
     *
     * @return array
     */
    public function getFormElementOptions()
    {
        return [];
    }

    /**
     * @return string
     */
    public function getFieldGroup()
    {
        return 'other';
    }

    /**
     * @param array $element
     *
     * @return array
     */
    public function getRawData($element)
    {
        $rawValue = $this->getRawValue($element['value'] ?? null);
        if (is_array($rawValue)) {
            $rowData = $rawValue;
        } else {
            $rowData = ['value' => $rawValue];
        }

        return array_merge($rowData, [
            'extra' => $element['extra'] ?? [],
        ]);
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
