<?php

namespace Sherlockode\AdvancedContentBundle\Form\DataTransformer;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;

class SerializedStringToEntities implements DataTransformerInterface
{
    /**
     * @var ObjectManager
     */
    private $om;

    /**
     * @var string
     */
    private $entityClass;

    /**
     * @var string
     */
    private $identifierField;

    /**
     * @param ObjectManager $om
     * @param string        $entityClass
     * @param string        $identifierField
     */
    public function __construct(ObjectManager $om, $entityClass, $identifierField)
    {
        $this->om = $om;
        $this->entityClass = $entityClass;
        $this->identifierField = $identifierField;
    }

    /**
     * Transforms a serialized string into an array of entities
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
            $entityValues = unserialize($valueAsString);
        } catch (\Exception $e) {
            return null;
        }

        $entities = [];
        foreach ($entityValues as $entityValue) {
            $entity = $this->om->getRepository($this->entityClass)->findOneBy([
                $this->identifierField => $entityValue,
            ]);
            if ($entity instanceof $this->entityClass) {
                $entities[] = $entity;
            }
        }

        return $entities;
    }

    /**
     * Transforms an array of entities into a serialized string
     *
     * @param array $entities
     *
     * @return string
     */
    public function reverseTransform($entities)
    {
        if (empty($entities)) {
            return serialize([]);
        }

        $propertyAccessor = PropertyAccess::createPropertyAccessor();
        $entityValues = [];
        foreach ($entities as $entity) {
            $entityValues[] = $propertyAccessor->getValue($entity, $this->identifierField);
        }

        return serialize($entityValues);
    }
}
