<?php

namespace Sherlockode\AdvancedContentBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Sherlockode\AdvancedContentBundle\Manager\ConfigurationManager;
use Sherlockode\AdvancedContentBundle\Manager\VersionManager;
use Sherlockode\AdvancedContentBundle\Model\ContentInterface;
use Sherlockode\AdvancedContentBundle\Model\PageInterface;
use Sherlockode\AdvancedContentBundle\Model\PageMetaInterface;

class PageListener
{
    /**
     * @var ConfigurationManager
     */
    private $configurationManager;

    /**
     * @var VersionManager
     */
    private $versionManager;

    /**
     * @param ConfigurationManager $configurationManager
     * @param VersionManager       $versionManager
     */
    public function __construct(ConfigurationManager $configurationManager, VersionManager $versionManager)
    {
        $this->configurationManager = $configurationManager;
        $this->versionManager = $versionManager;
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function postLoad(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if (!$entity instanceof PageInterface) {
            return;
        }

        $pageVersion = $this->versionManager->getPageVersionToLoad($entity);
        if ($pageVersion === null) {
            return;
        }

        $pageMetaVersion = $pageVersion->getPageMetaVersion();
        if ($pageMetaVersion !== null) {
            foreach ($entity->getPageMeta()->getVersions() as $version) {
                if ($version->getId() === $pageMetaVersion->getId()) {
                    $entity->getPageMeta()->setTitle($version->getTitle());
                    $entity->getPageMeta()->setSlug($version->getSlug());
                    $entity->getPageMeta()->setMetaTitle($version->getMetaTitle());
                    $entity->getPageMeta()->setMetaDescription($version->getMetaDescription());
                    break;
                }
            }
        }

        $contentVersion = $pageVersion->getContentVersion();
        if ($contentVersion !== null) {
            foreach ($entity->getContent()->getVersions() as $version) {
                if ($version->getId() === $contentVersion->getId()) {
                    $entity->getContent()->setData($contentVersion->getData());
                    break;
                }
            }
        }
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

        $pages = [];
        foreach ($entities as $entity) {
            if ($entity instanceof PageInterface) {
                $pages[$entity->getId()] = $entity;
                continue;
            }
            if ($entity instanceof PageMetaInterface && $entity->getPage() !== null && $entity->getPage()->getId()) {
                $pages[$entity->getPage()->getId()] = $entity->getPage();
                continue;
            }
            if ($entity instanceof ContentInterface && $entity->getPage() !== null && $entity->getPage()->getId()) {
                $pages[$entity->getPage()->getId()] = $entity->getPage();
            }
        }

        $pageVersionClassMetadata = $em->getClassMetadata($this->configurationManager->getEntityClass('page_version'));
        $pageClassMetadata = $em->getClassMetadata($this->configurationManager->getEntityClass('page'));
        $pageMetaVersionClassMetadata = $em->getClassMetadata($this->configurationManager->getEntityClass('page_meta_version'));
        $contentVersionClassMetadata = $em->getClassMetadata($this->configurationManager->getEntityClass('content_version'));
        foreach ($pages as $page) {
            $pageVersion = $this->versionManager->getNewPageVersion($page);
            $em->persist($pageVersion);
            $uow->computeChangeSet($pageVersionClassMetadata, $pageVersion);
            if ($contentVersion = $pageVersion->getContentVersion()) {
                $em->persist($contentVersion);
                $uow->computeChangeSet($contentVersionClassMetadata, $contentVersion);
            }
            if ($pageMetaVersion = $pageVersion->getPageMetaVersion()) {
                $em->persist($pageMetaVersion);
                $uow->computeChangeSet($pageMetaVersionClassMetadata, $pageMetaVersion);
            }
            $uow->recomputeSingleEntityChangeSet($pageClassMetadata, $page);
        }
    }
}
