<?php

namespace Sherlockode\AdvancedContentBundle\FieldType;

use Sherlockode\AdvancedContentBundle\Element\ElementInterface;
use Symfony\Component\Form\DataTransformerInterface;

interface FieldTypeInterface extends ElementInterface
{
    /**
     * Get options to apply on element
     *
     * @return array
     */
    public function getFormElementOptions();

    /**
     * Get model transformer for value field
     *
     * @return DataTransformerInterface|null
     */
    public function getValueModelTransformer();

    /**
     * @return string
     */
    public function getFieldGroup();

    /**
     * @param array $data
     *
     * @return $this
     */
    public function setConfigData(array $data);

    /**
     * @param mixed $element
     *
     * @return mixed
     */
    public function getRawValue($element);
}
