<?php

namespace Sherlockode\AdvancedContentBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Sherlockode\AdvancedContentBundle\Manager\ConfigurationManager;
use Sherlockode\AdvancedContentBundle\Manager\ContentVersionManager;
use Sherlockode\AdvancedContentBundle\Model\ContentInterface;

class ContentListener
{
    /**
     * @var ConfigurationManager
     */
    private $configurationManager;

    /**
     * @var ContentVersionManager
     */
    private $contentVersionManager;

    /**
     * @param ConfigurationManager  $configurationManager
     * @param ContentVersionManager $contentVersionManager
     */
    public function __construct(ConfigurationManager $configurationManager, ContentVersionManager $contentVersionManager)
    {
        $this->configurationManager = $configurationManager;
        $this->contentVersionManager = $contentVersionManager;
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function postLoad(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if (!$entity instanceof ContentInterface) {
            return;
        }

        $entity->setData($this->contentVersionManager->getContentData($entity));
    }

    /**
     * @param OnFlushEventArgs $args
     */
    public function onFlush(OnFlushEventArgs $args)
    {
        $em = $args->getEntityManager();
        $uow = $em->getUnitOfWork();

        $entities = [
            ...$uow->getScheduledEntityInsertions(),
            ...$uow->getScheduledEntityUpdates()
        ];

        $contentVersionClassMetadata = $em->getClassMetadata($this->configurationManager->getEntityClass('content_version'));
        $contentClassMetadata = $em->getClassMetadata($this->configurationManager->getEntityClass('content'));
        foreach ($entities as $entity) {
            if (!$entity instanceof ContentInterface) {
                continue;
            }

            $contentVersion = $this->contentVersionManager->getNewContentVersion($entity);
            $em->persist($contentVersion);
            $uow->computeChangeSet($contentVersionClassMetadata, $contentVersion);
            $uow->recomputeSingleEntityChangeSet($contentClassMetadata, $entity);
        }
    }
}
