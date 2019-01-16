<?php

namespace Sherlockode\AdvancedContentBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

class StringToDateTimeTransformer implements DataTransformerInterface
{
    /**
     * Transforms a string into a DateTime
     *
     * @param string|null $valueAsString
     *
     * @return \DateTime|null
     */
    public function transform($valueAsString)
    {
        if ($valueAsString === null) {
            return null;
        }

        return new \DateTime($valueAsString);
    }

    /**
     * Transforms a DateTime into a string
     *
     * @param \DateTime|null $valueAsDate
     *
     * @return string|null
     */
    public function reverseTransform($valueAsDate)
    {
        if (!$valueAsDate instanceof \DateTime) {
            return null;
        }

        return $valueAsDate->format('Y-m-d H:i:s');
    }
}
