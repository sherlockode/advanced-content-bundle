<?php

namespace Sherlockode\AdvancedContentBundle\Form\DataTransformer;

use Doctrine\Common\Collections\ArrayCollection;
use Sherlockode\AdvancedContentBundle\Model\ContentInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * Class LocaleContentsTransformer
 */
class LocaleContentsTransformer implements DataTransformerInterface
{
    /**
     * Transforms a list of Content into an array with the Content locale as key
     *
     * @param mixed $value
     *
     * @return array
     */
    public function transform($value)
    {
        if (!$value instanceof \Traversable && !is_array($value)) {
            return $value;
        }
        $valueMap = [];

        foreach ($value as $data) {
            if (!$data instanceof ContentInterface) {
                throw new TransformationFailedException(sprintf(
                    'The elements should be of type %s, %s received',
                    ContentInterface::class,
                    is_object($data) ? get_class($data) : gettype($data)
                ));
            }

            $valueMap[$data->getLocale()] = $data;
        }

        return $valueMap;
    }

    /**
     * Transforms an array into a Collection.
     * No need to process the array keys here (only used in the form view)
     *
     * @param mixed $value
     *
     * @return ArrayCollection
     */
    public function reverseTransform($value)
    {
        if (!is_array($value)) {
            $value = [];
        }

        return new ArrayCollection($value);
    }
}
