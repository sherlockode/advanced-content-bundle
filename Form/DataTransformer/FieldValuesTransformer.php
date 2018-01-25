<?php

namespace Sherlockode\AdvancedContentBundle\Form\DataTransformer;

use Sherlockode\AdvancedContentBundle\Manager\ContentManager;
use Sherlockode\AdvancedContentBundle\Model\FieldValue;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Form\DataTransformerInterface;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Sherlockode\AdvancedContentBundle\Model\Content;

class FieldValuesTransformer implements DataTransformerInterface
{
    /**
     * @var Content
     */
    private $content;

    /**
     * @var ContentManager
     */
    private $contentManager;

    /**
     * FieldValuesTransformer constructor.
     *
     * @param ContentManager $contentManager
     * @param Content $content
     */
    public function __construct(ContentManager $contentManager, Content $content)
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
            if (!$fieldValue instanceof FieldValue) {
                throw new TransformationFailedException('Expected a ' . FieldValue::class . ' object.');
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
