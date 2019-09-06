<?php

namespace Sherlockode\AdvancedContentBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Sherlockode\AdvancedContentBundle\Manager\PageManager;
use Sherlockode\AdvancedContentBundle\Model\ContentInterface;
use Sherlockode\AdvancedContentBundle\Model\PageInterface;

class PageListener
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
    public function prePersist(LifecycleEventArgs $args)
    {
        $object = $args->getObject();

        if (!$object instanceof PageInterface) {
            return;
        }

        if ($object->getStatus() === null) {
            $object->setStatus(PageInterface::STATUS_DRAFT);
        }
        if (!$object->getContent() instanceof ContentInterface) {
            $this->pageManager->createContentForPage($object);
        }
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function preUpdate(LifecycleEventArgs $args)
    {
        $object = $args->getObject();

        if (!$object instanceof PageInterface) {
            return;
        }

        $this->shouldFlush = $this->pageManager->updateContentForPage($object);
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function postUpdate(LifecycleEventArgs $args)
    {
        $object = $args->getObject();

        if (!$object instanceof PageInterface) {
            return;
        }

        if (!$this->shouldFlush) {
            return;
        }

        $entityManager = $args->getEntityManager();
        $entityManager->flush();
    }
}
