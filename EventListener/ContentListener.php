<?php

namespace Sherlockode\AdvancedContentBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Sherlockode\AdvancedContentBundle\Manager\ConfigurationManager;
use Sherlockode\AdvancedContentBundle\Manager\VersionManager;
use Sherlockode\AdvancedContentBundle\Model\ContentInterface;

class ContentListener
{
    /**
     * @var ConfigurationManager
     */
    private $configurationManager;

    /**
     * @var VersionManager
     */
    private $versionManager;

    public function __construct(ConfigurationManager $configurationManager, VersionManager $versionManager)
    {
        $this->configurationManager = $configurationManager;
        $this->versionManager = $versionManager;
    }

    public function postLoad(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if (!$entity instanceof ContentInterface) {
            return;
        }

        if (null !== $entity->getPage()) {
            return;
        }

        $entity->setData($this->versionManager->getContentData($entity), false);
    }

    public function onFlush(OnFlushEventArgs $args)
    {
        $em = $args->getEntityManager();
        $uow = $em->getUnitOfWork();

        $entities = [
            ...$uow->getScheduledEntityInsertions(),
            ...$uow->getScheduledEntityUpdates(),
        ];

        $contentVersionClassMetadata = $em->getClassMetadata($this->configurationManager->getEntityClass('content_version'));
        $contentClassMetadata = $em->getClassMetadata($this->configurationManager->getEntityClass('content'));
        foreach ($entities as $entity) {
            if (!$entity instanceof ContentInterface) {
                continue;
            }

            if (null !== $entity->getPage()) {
                continue;
            }

            $contentVersion = $this->versionManager->getNewContentVersion($entity);
            $em->persist($contentVersion);
            $uow->computeChangeSet($contentVersionClassMetadata, $contentVersion);
            $uow->recomputeSingleEntityChangeSet($contentClassMetadata, $entity);
        }
    }
}
