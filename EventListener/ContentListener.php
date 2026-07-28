<?php

declare(strict_types=1);

namespace Sherlockode\AdvancedContentBundle\EventListener;

use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Event\PostLoadEventArgs;
use Sherlockode\AdvancedContentBundle\Manager\ConfigurationManager;
use Sherlockode\AdvancedContentBundle\Manager\VersionManager;
use Sherlockode\AdvancedContentBundle\Model\ContentInterface;

class ContentListener
{
    public function __construct(
        private readonly ConfigurationManager $configurationManager,
        private readonly VersionManager $versionManager,
    ) {
    }

    public function postLoad(PostLoadEventArgs $args): void
    {
        $entity = $args->getObject();

        if (!$entity instanceof ContentInterface) {
            return;
        }

        if (null !== $entity->getPage()) {
            return;
        }

        $entity->setData($this->versionManager->getContentData($entity), false);
    }

    public function onFlush(OnFlushEventArgs $args): void
    {
        $em = $args->getObjectManager();
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
