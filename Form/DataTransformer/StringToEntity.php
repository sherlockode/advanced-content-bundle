<?php

namespace Sherlockode\AdvancedContentBundle\Form\DataTransformer;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;

class StringToEntity implements DataTransformerInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var string
     */
    private $entityClass;

    /**
     * @var string
     */
    private $identifierField;

    /**
     * @param EntityManagerInterface $oe
     * @param string                 $entityClass
     * @param string                 $identifierField
     */
    public function __construct(EntityManagerInterface $em, $entityClass, $identifierField)
    {
        $this->em = $em;
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

        $entity = $this->em->getRepository($this->entityClass)->findOneBy([
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
