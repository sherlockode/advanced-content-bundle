<?php

namespace Sherlockode\AdvancedContentBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Sherlockode\AdvancedContentBundle\Manager\PageManager;
use Sherlockode\AdvancedContentBundle\Model\PageTypeInterface;

class PageTypeListener
{
    /**
     * @var PageManager
     */
    private $pageManager;

    /**
     * @var bool
     */
    private $shouldFlush = false;

    /**
     * @param PageManager $pageManager
     */
    public function __construct(PageManager $pageManager)
    {
        $this->pageManager = $pageManager;
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function preRemove(LifecycleEventArgs $args)
    {
        $object = $args->getObject();

        if (!$object instanceof PageTypeInterface) {
            return;
        }

        $this->shouldFlush = $this->pageManager->updatePagesAfterPageTypeRemove($object);
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function postRemove(LifecycleEventArgs $args)
    {
        $object = $args->getObject();

        if (!$object instanceof PageTypeInterface) {
            return;
        }

        if (!$this->shouldFlush) {
            return;
        }

        $entityManager = $args->getEntityManager();
        $entityManager->flush();
    }
}
