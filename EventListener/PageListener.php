<?php

namespace Sherlockode\AdvancedContentBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Sherlockode\AdvancedContentBundle\Model\PageInterface;

class PageListener
{
    /**
     * @param LifecycleEventArgs $args
     */
    public function prePersist(LifecycleEventArgs $args)
    {
        $object = $args->getObject();

        if (!$object instanceof PageInterface) {
            return;
        }

        if ($object->getStatus() === null) {
            $object->setStatus(PageInterface::STATUS_DRAFT);
        }
    }
}
