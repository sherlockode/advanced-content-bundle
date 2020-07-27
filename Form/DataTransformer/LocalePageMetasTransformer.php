<?php

namespace Sherlockode\AdvancedContentBundle\Form\DataTransformer;

use Doctrine\Common\Collections\ArrayCollection;
use Sherlockode\AdvancedContentBundle\Model\PageMetaInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * Class LocalePageMetasTransformer
 */
class LocalePageMetasTransformer implements DataTransformerInterface
{
    /**
     * Transforms a list of PageMeta into an array with the PageMeta locale as key
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
            if (!$data instanceof PageMetaInterface) {
                throw new TransformationFailedException(sprintf(
                    'The elements should be of type %s, %s received',
                    PageMetaInterface::class,
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
