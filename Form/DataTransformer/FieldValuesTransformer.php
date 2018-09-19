<?php

namespace Sherlockode\AdvancedContentBundle\Form\DataTransformer;

use Sherlockode\AdvancedContentBundle\Manager\ContentManager;
use Sherlockode\AdvancedContentBundle\Model\ContentTypeInterface;
use Sherlockode\AdvancedContentBundle\Model\FieldValueInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Form\DataTransformerInterface;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

class FieldValuesTransformer implements DataTransformerInterface
{
    /**
     * @var ContentTypeInterface
     */
    private $contentType;

    /**
     * @var ContentManager
     */
    private $contentManager;

    /**
     * FieldValuesTransformer constructor.
     *
     * @param ContentManager       $contentManager
     * @param ContentTypeInterface $contentType
     */
    public function __construct(ContentManager $contentManager, ContentTypeInterface $contentType)
    {
        $this->contentManager = $contentManager;
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
            throw new TransformationFailedException('Expected a ' . Collection::class . ' object.');
        }

        $values = [];
        foreach ($collection as $fieldValue) {
            $values[$fieldValue->getField()->getId()] = $fieldValue;
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
        foreach ($array as $fieldId => $fieldValue) {
            if ($fieldValue === null) {
                continue;
            }
            if (!$fieldValue instanceof FieldValueInterface) {
                throw new TransformationFailedException('Expected a ' . FieldValueInterface::class . ' object.');
            }
            if (!$fieldValue->getField()) {
                $field = $this->contentManager->getFieldById($this->contentType, $fieldId);
                if ($field === null) {
                    continue;
                }
                $fieldValue->setField($field);
            }
            $values[] = $fieldValue;
        }

        return new ArrayCollection($values);
    }
}
