<?php

namespace Sherlockode\AdvancedContentBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Sherlockode\AdvancedContentBundle\Manager\FieldManager;
use Sherlockode\AdvancedContentBundle\Model\FieldValueInterface;

class FieldValueListener
{
    /**
     * @var FieldManager
     */
    private $fieldManager;

    public function __construct(FieldManager $fieldManager)
    {
        $this->fieldManager = $fieldManager;
    }

    public function preUpdate(LifecycleEventArgs $args)
    {
        $object = $args->getObject();

        if (!$object instanceof FieldValueInterface) {
            return;
        }

        $em = $args->getEntityManager();
        $uow = $em->getUnitOfWork();
        $changeset = $uow->getEntityChangeSet($object);

        $this->fieldManager->getFieldType($object->getField())->updateFieldValueValue($object, $changeset);
    }
}
