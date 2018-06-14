<?php

namespace Sherlockode\AdvancedContentBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

class SerializedStringToStringTransformer implements DataTransformerInterface
{
    /**
     * Transforms a serialized string into a string
     *
     * @param string $valueAsString
     *
     * @return string
     */
    public function transform($valueAsString)
    {
        if (empty($valueAsString)) {
            return '';
        }
        $valueAsArray = unserialize($valueAsString);

        return isset($valueAsArray[0]) ? $valueAsArray[0] : '';
    }

    /**
     * Transforms a string into a serialized string
     *
     * @param string $valueAsString
     *
     * @return string
     */
    public function reverseTransform($valueAsString)
    {
        if (empty($valueAsString)) {
            $valueAsString = [];
        }
        if (!is_array($valueAsString)) {
            $valueAsString = [$valueAsString];
        }
        return serialize($valueAsString);
    }
}
