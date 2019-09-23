<?php

namespace Sherlockode\AdvancedContentBundle\Form\DataTransformer;

use Doctrine\ORM\EntityRepository;
use Sherlockode\AdvancedContentBundle\Model\LayoutInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class LayoutToIdTransformer implements DataTransformerInterface
{
    /**
     * @var EntityRepository
     */
    private $entityRepository;

    /**
     * @param EntityRepository $entityRepository
     */
    public function __construct(EntityRepository $entityRepository)
    {
        $this->entityRepository = $entityRepository;
    }

    /**
     * Transforms a layout into an id
     *
     * @param LayoutInterface $layout
     *
     * @return int
     */
    public function transform($layout)
    {
        if (!$layout instanceof LayoutInterface) {
            return null;
        }

        return $layout->getId();
    }

    /**
     * Transforms an id into a layout
     *
     * @param int $layoutId
     *
     * @return string
     */
    public function reverseTransform($layoutId)
    {
        if (empty($layoutId)) {
            return null;
        }

        $layout = $this->entityRepository->find($layoutId);

        if (!$layout instanceof LayoutInterface) {
            throw new TransformationFailedException(sprintf('Could not find a %s object with id %s', LayoutInterface::class, $layoutId));
        }

        return $layout;
    }
}
