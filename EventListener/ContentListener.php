<?php

declare(strict_types=1);

namespace Sherlockode\AdvancedContentBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Sherlockode\AdvancedContentBundle\Manager\ConfigurationManager;
use Sherlockode\AdvancedContentBundle\Manager\VersionManager;
use Sherlockode\AdvancedContentBundle\Model\ContentInterface;

class ContentListener
{
    public function __construct(private readonly ConfigurationManager $configurationManager, private readonly VersionManager $versionManager)
    {
    }

    public function postLoad(LifecycleEventArgs $args): void
    {
        $entity = $args->getEntity();

        if (!$entity instanceof ContentInterface) {
            return;
        }

        if ($entity->getPage() !== null) {
            return;
        }

        $entity->setData($this->versionManager->getContentData($entity), false);
    }

    public function onFlush(OnFlushEventArgs $args): void
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

            if ($entity->getPage() !== null) {
                continue;
            }

            $contentVersion = $this->versionManager->getNewContentVersion($entity);
            $em->persist($contentVersion);
            $uow->computeChangeSet($contentVersionClassMetadata, $contentVersion);
            $uow->recomputeSingleEntityChangeSet($contentClassMetadata, $entity);
        }
    }
}
