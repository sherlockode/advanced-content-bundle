<?php

namespace Sherlockode\AdvancedContentBundle\Manager;

use Sherlockode\AdvancedContentBundle\Model\ContentInterface;
use Sherlockode\AdvancedContentBundle\Model\ContentTypeInterface;
use Sherlockode\AdvancedContentBundle\Model\FieldInterface;
use Doctrine\Common\Persistence\ObjectManager;

class ContentManager
{
    /**
     * @var ConfigurationManager
     */
    private $configurationManager;

    /**
     * @var ObjectManager
     */
    private $om;

    /**
     * ContentManager constructor.
     *
     * @param ConfigurationManager $configurationManager
     * @param ObjectManager        $om
     */
    public function __construct(ConfigurationManager $configurationManager, ObjectManager $om)
    {
        $this->configurationManager = $configurationManager;
        $this->om = $om;
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
        $field = $this->om->getRepository($this->configurationManager->getEntityClass('field'))->find($fieldId);


        if (!$field instanceof FieldInterface) {
            return null;
        }
        if ($field->getContentType() && $field->getContentType()->getId() == $contentType->getId()) {
            return $field;
        }

        while ($parent = $field->getParent()) {
            if ($parent->getContentType() !== null && $parent->getContentType()->getId() == $contentType->getId()) {
                return $field;
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
        return $this->om->getRepository($this->configurationManager->getEntityClass('content'))->find($id);
    }

    /**
     * Get all contents
     *
     * @return array
     */
    public function getContents()
    {
        return $this->om->getRepository($this->configurationManager->getEntityClass('content'))->findAll();
    }
}
