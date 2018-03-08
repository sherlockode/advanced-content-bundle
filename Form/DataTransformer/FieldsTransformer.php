<?php

namespace Sherlockode\AdvancedContentBundle\Form\DataTransformer;

use Sherlockode\AdvancedContentBundle\Model\ContentTypeInterface;
use Sherlockode\AdvancedContentBundle\Model\FieldInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Form\DataTransformerInterface;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

class FieldsTransformer implements DataTransformerInterface
{
    /**
     * @var ContentTypeInterface
     */
    private $contentType;

    /**
     * FieldValuesTransformer constructor.
     *
     * @param ContentTypeInterface $contentType
     */
    public function __construct(ContentTypeInterface $contentType)
    {
        $this->contentType = $contentType;
    }

    /**
     * Transforms a collection into an array.
     *
     * @param Collection $collection
     *
     * @return array
     *
     * @throws TransformationFailedException
     */
    public function transform($collection)
    {
        if (null === $collection) {
            return [];
        }

        if (!$collection instanceof Collection) {
            throw new TransformationFailedException(sprintf('Expected a %s object.', Collection::class));
        }

        $values = [];
        foreach ($collection as $field) {
            $values[$field->getSlug()] = $field;
        }

        return $values;
    }

    /**
     * Transforms choice keys into entities.
     *
     * @param mixed $array An array of entities
     *
     * @return Collection A collection of entities
     *
     * @throws TransformationFailedException
     */
    public function reverseTransform($array)
    {
        if ('' === $array || null === $array) {
            $array = [];
        }

        $values = [];
        foreach ($array as $slug => $field) {
            if ($field === null) {
                continue;
            }
            if (!$field instanceof FieldInterface) {
                throw new TransformationFailedException(sprintf('Expected a %s object.', FieldInterface::class));
            }
            if (!$field->getContentType()) {
                $field->setContentType($this->contentType);
            }
            $values[] = $field;
        }

        return new ArrayCollection($values);
    }
}
