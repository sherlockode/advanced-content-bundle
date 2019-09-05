<?php

namespace Sherlockode\AdvancedContentBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Sherlockode\AdvancedContentBundle\Manager\PageManager;
use Sherlockode\AdvancedContentBundle\Model\ContentTypeInterface;

class ContentTypeListener
{
    /**
     * @var PageManager
     */
    private $pageManager;

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
    public function postPersist(LifecycleEventArgs $args)
    {
        $object = $args->getObject();

        if (!$object instanceof ContentTypeInterface) {
            return;
        }

        $shouldFlush = $this->pageManager->updatePages();

        if (!$shouldFlush) {
            return;
        }

        $entityManager = $args->getEntityManager();
        $entityManager->flush();
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function postUpdate(LifecycleEventArgs $args)
    {
        $object = $args->getObject();

        if (!$object instanceof ContentTypeInterface) {
            return;
        }

        $shouldFlush = $this->pageManager->updatePages();

        if (!$shouldFlush) {
            return;
        }

        $entityManager = $args->getEntityManager();
        $entityManager->flush();
    }
}
