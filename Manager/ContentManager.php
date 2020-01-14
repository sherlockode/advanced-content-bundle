<?php

namespace Sherlockode\AdvancedContentBundle\Manager;

use Doctrine\ORM\EntityManagerInterface;
use Sherlockode\AdvancedContentBundle\Model\ContentInterface;
use Sherlockode\AdvancedContentBundle\Model\ContentTypeInterface;
use Sherlockode\AdvancedContentBundle\Model\FieldInterface;

class ContentManager
{
    /**
     * @var ConfigurationManager
     */
    private $configurationManager;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * ContentManager constructor.
     *
     * @param ConfigurationManager   $configurationManager
     * @param EntityManagerInterface $em
     */
    public function __construct(ConfigurationManager $configurationManager, EntityManagerInterface $em)
    {
        $this->configurationManager = $configurationManager;
        $this->em = $em;
    }

    /**
     * Get field matching slug
     *
     * @param ContentTypeInterface $contentType
     * @param int                  $fieldId
     *
     * @return FieldInterface|null
     */
    public function getFieldById(ContentTypeInterface $contentType, $fieldId)
    {
        $field = $this->em->getRepository($this->configurationManager->getEntityClass('field'))->find($fieldId);


        if (!$field instanceof FieldInterface) {
            return null;
        }
        if ($field->getContentType() && $field->getContentType()->getId() == $contentType->getId()) {
            return $field;
        }

        $parent = $field;
        while ($parent && $parentLayout = $parent->getLayout()) {
            if ($parent = $parentLayout->getParent()) {
                if ($parent->getContentType() !== null && $parent->getContentType()->getId() == $contentType->getId()) {
                    return $field;
                }
            }
        }

        return null;
    }

    /**
     * Get content by its id
     *
     * @param int $id
     *
     * @return null|ContentInterface
     */
    public function getContentById($id)
    {
        return $this->em->getRepository($this->configurationManager->getEntityClass('content'))->find($id);
    }

    /**
     * Get all contents
     *
     * @return array
     */
    public function getContents()
    {
        return $this->em->getRepository($this->configurationManager->getEntityClass('content'))->findAll();
    }
}
