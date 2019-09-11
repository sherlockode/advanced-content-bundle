<?php

namespace Sherlockode\AdvancedContentBundle\Manager;

use Sherlockode\AdvancedContentBundle\Model\ContentTypeInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Sherlockode\AdvancedContentBundle\Model\FieldInterface;

class ContentTypeManager
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
     * Get content type by its id
     *
     * @param int $id
     *
     * @return null|ContentTypeInterface
     */
    public function getContentTypeById($id)
    {
        return $this->om->getRepository($this->configurationManager->getEntityClass('content_type'))->find($id);
    }

    /**
     * Get content type ordered fields
     *
     * @param ContentTypeInterface $contentType
     *
     * @return FieldInterface[]
     */
    public function getOrderedFields(ContentTypeInterface $contentType)
    {
        return $this->om->getRepository($this->configurationManager->getEntityClass('field'))
            ->findBy(['contentType' => $contentType], ['position' => 'ASC']);
    }

    /**
     * Get all content types
     *
     * @return array
     */
    public function getContentTypes()
    {
        return $this->om->getRepository($this->configurationManager->getEntityClass('content_type'))->findAll();
    }

    /**
     * When field type has changed, remove it and create new one
     * By cascading, will also remove associated fieldValues
     *
     * @param ContentTypeInterface $contentType
     * @param array                $fieldTypes
     */
    public function processFieldsChangeType(ContentTypeInterface $contentType, $fieldTypes)
    {
        foreach ($contentType->getFields() as $field) {
            if (!isset($fieldTypes[$field->getId()])) {
                continue;
            }
            if ($fieldTypes[$field->getId()] == $field->getType()) {
                continue;
            }

            $this->om->remove($field);

            $fieldClass = $this->configurationManager->getEntityClass('field');
            /** @var FieldInterface $newField */
            $newField = new $fieldClass;
            $newField->setType($field->getType());
            $newField->setContentType($field->getContentType());
            $newField->setName($field->getName());
            $newField->setSlug($field->getSlug());
            $newField->setRequired($field->isRequired());
            $newField->setPosition($field->getPosition());
            $newField->setOptions($field->getOptions());
            $newField->setHint($field->getHint());
            $this->om->persist($newField);
        }
    }

    /**
     * @param ContentTypeInterface $contentType
     *
     * @return bool
     */
    public function canCreateContent(ContentTypeInterface $contentType)
    {
        if ($contentType->isAllowSeveralContents()) {
            return true;
        }

        $contents = $this->om->getRepository($this->configurationManager->getEntityClass('content'))->findBy([
            'contentType' => $contentType
        ]);

        return count($contents) === 0;
    }

    /**
     * @param string $contentTypeId
     *
     * @return bool
     */
    public function canCreateContentByContentTypeId($contentTypeId)
    {
        return $this->canCreateContent($this->getContentTypeById((int)$contentTypeId));
    }
}
