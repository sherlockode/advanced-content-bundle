<?php

namespace Sherlockode\AdvancedContentBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Sherlockode\AdvancedContentBundle\Model\ContentVersionInterface;

class ContentVersionListener
{
    /**
     * @param LifecycleEventArgs $args
     */
    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if (!$entity instanceof ContentVersionInterface) {
            return;
        }
        if (!$entity->isAutoSave()) {
            return;
        }

        $count = 0;
        foreach ($entity->getContent()->getVersions() as $version) {
            if (!$version->isAutoSave()) {
                continue;
            }
            if ($version->getUserId() !== $entity->getUserId()) {
                continue;
            }
            if ($version->getCreatedAt() < $entity->getCreatedAt()) {
                $count++;
            }
            if ($count >= 10) {
                // Keep only the last 10 drafts by same user
                $args->getEntityManager()->remove($version);
            }
        }
    }
}
