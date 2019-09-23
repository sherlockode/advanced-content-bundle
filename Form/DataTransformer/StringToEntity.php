<?php

namespace Sherlockode\AdvancedContentBundle\Form\DataTransformer;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;

class StringToEntity implements DataTransformerInterface
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
     * Transforms a string into an entity
     *
     * @param string $valueAsString
     *
     * @return object|null
     */
    public function transform($valueAsString)
    {
        if (empty($valueAsString)) {
            return null;
        }

        $entity = $this->om->getRepository($this->entityClass)->findOneBy([
            $this->identifierField => $valueAsString,
        ]);

        return $entity;
    }

    /**
     * Transforms an entity into a string
     *
     * @param object $entity
     *
     * @return string
     */
    public function reverseTransform($entity)
    {
        if (empty($entity)) {
            return null;
        }

        $propertyAccessor = PropertyAccess::createPropertyAccessor();

        return $propertyAccessor->getValue($entity, $this->identifierField);
    }
}
