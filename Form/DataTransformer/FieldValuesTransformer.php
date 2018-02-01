<?php

namespace Sherlockode\AdvancedContentBundle\Form\DataTransformer;

use Sherlockode\AdvancedContentBundle\Manager\ContentManager;
use Sherlockode\AdvancedContentBundle\Model\ContentInterface;
use Sherlockode\AdvancedContentBundle\Model\FieldValueInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Form\DataTransformerInterface;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

class FieldValuesTransformer implements DataTransformerInterface
{
    /**
     * @var ContentInterface
     */
    private $content;

    /**
     * @var ContentManager
     */
    private $contentManager;

    /**
     * FieldValuesTransformer constructor.
     *
     * @param ContentManager   $contentManager
     * @param ContentInterface $content
     */
    public function __construct(ContentManager $contentManager, ContentInterface $content)
    {
        $this->contentManager = $contentManager;
        $this->content = $content;
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
            $values[$fieldValue->getField()->getSlug()] = $fieldValue;
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
        foreach ($array as $slug => $fieldValue) {
            if ($fieldValue === null) {
                continue;
            }
            if (!$fieldValue instanceof FieldValueInterface) {
                throw new TransformationFailedException('Expected a ' . FieldValueInterface::class . ' object.');
            }
            if (!$fieldValue->getField()) {
                $field = $this->contentManager->getFieldBySlug($this->content, $slug);
                if ($field === null) {
                    continue;
                }
                $fieldValue->setField($field);
                $fieldValue->setContent($this->content);
            }
            $values[] = $fieldValue;
        }

        return new ArrayCollection($values);
    }
}
