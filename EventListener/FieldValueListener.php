<?php

namespace Sherlockode\AdvancedContentBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Sherlockode\AdvancedContentBundle\Manager\FieldManager;
use Sherlockode\AdvancedContentBundle\Manager\UploadManager;
use Sherlockode\AdvancedContentBundle\Model\FieldValueInterface;

class FieldValueListener
{
    /**
     * @var FieldManager
     */
    private $fieldManager;

    /**
     * @var UploadManager
     */
    private $uploadManager;

    /**
     * @param FieldManager  $fieldManager
     * @param UploadManager $uploadManager
     */
    public function __construct(FieldManager $fieldManager, UploadManager $uploadManager)
    {
        $this->fieldManager = $fieldManager;
        $this->uploadManager = $uploadManager;
    }

    public function preUpdate(LifecycleEventArgs $args)
    {
        $object = $args->getObject();

        if (!$object instanceof FieldValueInterface) {
            return;
        }

        if (!in_array($this->fieldManager->getFieldTypeByCode($object->getFieldType())->getCode(), ['file', 'image'])) {
            return;
        }

        $em = $args->getObjectManager();
        $uow = $em->getUnitOfWork();
        $changeSet = $uow->getEntityChangeSet($object);

        if (!isset($changeSet['value'])) {
            return;
        }
        $oldValue = unserialize($changeSet['value'][0]);
        $newValue = unserialize($changeSet['value'][1]);

        if (empty($newValue['delete'])) {
            return;
        }

        if (!empty($oldValue['src'])) {
            $this->uploadManager->remove($oldValue['src']);
        }
        unset($newValue['delete']);
        unset($newValue['src']);

        $object->setValue($newValue);
    }
}
