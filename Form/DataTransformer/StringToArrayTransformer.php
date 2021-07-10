<?php

namespace Sherlockode\AdvancedContentBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

class StringToArrayTransformer implements DataTransformerInterface
{
    /**
     * Transforms a string into an array
     *
     * @param string $valueAsString
     *
     * @return array
     */
    public function transform($valueAsString)
    {
        if (empty($valueAsString)) {
            return [];
        }
        try {
            return unserialize($valueAsString);
        } catch (\Throwable $e) {
            return [];
        }
    }

    /**
     * Transforms an array into a string
     *
     * @param array $valueAsArray
     *
     * @return string
     */
    public function reverseTransform($valueAsArray)
    {
        if (empty($valueAsArray)) {
            $valueAsArray = [];
        }
        if (!is_array($valueAsArray)) {
            $valueAsArray = [$valueAsArray];
        }
        return serialize($valueAsArray);
    }
}
