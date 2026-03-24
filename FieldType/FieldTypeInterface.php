<?php

namespace Sherlockode\AdvancedContentBundle\FieldType;

use Sherlockode\AdvancedContentBundle\Element\ElementInterface;
use Symfony\Component\Form\DataTransformerInterface;

interface FieldTypeInterface extends ElementInterface
{
    /**
     * Get options to apply on element.
     *
     * @return array
     */
    public function getFormElementOptions();

    /**
     * Get model transformer for value field.
     *
     * @return DataTransformerInterface|null
     */
    public function getValueModelTransformer();

    /**
     * @return string
     */
    public function getFieldGroup();

    /**
     * @return $this
     */
    public function setConfigData(array $data);

    public function getRawValue($element);
}
