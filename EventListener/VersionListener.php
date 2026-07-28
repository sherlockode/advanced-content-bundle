<?php

declare(strict_types=1);

namespace Sherlockode\AdvancedContentBundle\EventListener;

use Doctrine\ORM\Event\PrePersistEventArgs;
use Sherlockode\AdvancedContentBundle\Model\ContentVersionInterface;
use Sherlockode\AdvancedContentBundle\Model\PageVersionInterface;
use Sherlockode\AdvancedContentBundle\Model\VersionInterface;

class VersionListener
{
    public function prePersist(PrePersistEventArgs $args): void
    {
        $entity = $args->getObject();

        if (!$entity instanceof VersionInterface || !$entity->isAutoSave()) {
            return;
        }

        if ($entity instanceof ContentVersionInterface && null === $entity->getContent()->getPage()) {
            $versions = $entity->getContent()->getVersions();
        } elseif ($entity instanceof PageVersionInterface) {
            $versions = $entity->getPage()->getVersions();
        } else {
            return;
        }

        $count = 0;
        foreach ($versions as $version) {
            if (!$version->isAutoSave()) {
                continue;
            }

            if ($version->getUserId() !== $entity->getUserId()) {
                continue;
            }

            if ($version->getCreatedAt() < $entity->getCreatedAt()) {
                ++$count;
            }

            if ($count >= 10) {
                // Keep only the last 10 drafts by same user
                $args->getObjectManager()->remove($version);
            }
        }
    }
}
