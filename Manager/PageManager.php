<?php

namespace Sherlockode\AdvancedContentBundle\Manager;

use Doctrine\ORM\EntityManagerInterface;
use Sherlockode\AdvancedContentBundle\Model\ContentTypeInterface;
use Sherlockode\AdvancedContentBundle\Model\PageInterface;
use Sherlockode\AdvancedContentBundle\Model\PageMetaInterface;
use Sherlockode\AdvancedContentBundle\Model\PageTypeInterface;

class PageManager
{
    /**
     * @var ConfigurationManager
     */
    private $configurationManager;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @param ConfigurationManager   $configurationManager
     * @param EntityManagerInterface $em
     */
    public function __construct(ConfigurationManager $configurationManager, EntityManagerInterface $em)
    {
        $this->configurationManager = $configurationManager;
        $this->em = $em;
    }

    /**
     * @param PageInterface $page
     *
     * @return null|ContentTypeInterface
     */
    public function getPageContentType(PageInterface $page)
    {
        $contentTypeByPage = $this->em->getRepository($this->configurationManager->getEntityClass('content_type'))->findOneBy([
            'page' => $page
        ]);

        if ($contentTypeByPage instanceof ContentTypeInterface) {
            return $contentTypeByPage;
        }

        $pageType = $page->getPageType();
        if (!$pageType instanceof PageTypeInterface) {
            return null;
        }

        $contentTypeByPageType = $this->em->getRepository($this->configurationManager->getEntityClass('content_type'))->findOneBy([
            'pageType' => $pageType
        ]);

        if ($contentTypeByPageType instanceof ContentTypeInterface) {
            return $contentTypeByPageType;
        }

        return null;
    }

    /**
     * @param ContentTypeInterface $contentTypeToValidate
     * @param null|int             $pageTypeId
     * @param null|int             $pageId
     *
     * @return bool
     */
    public function validateContentTypeLink(ContentTypeInterface $contentTypeToValidate, $pageTypeId = null, $pageId = null)
    {
        $contentTypes = [];
        if ($pageTypeId !== null) {
            $pageType = $this->em->getRepository($this->configurationManager->getEntityClass('page_type'))->find($pageTypeId);
            if (!$pageType instanceof PageTypeInterface) {
                return false;
            }
            $contentTypes = $this->em->getRepository($this->configurationManager->getEntityClass('content_type'))->findBy([
                'pageType' => $pageType,
            ]);
        }
        if ($pageId !== null) {
            $page = $this->em->getRepository($this->configurationManager->getEntityClass('page'))->find($pageId);
            if (!$page instanceof PageInterface) {
                return false;
            }
            $contentTypes = $this->em->getRepository($this->configurationManager->getEntityClass('content_type'))->findBy([
                'page' => $page,
            ]);
        }
        if (!$contentTypeToValidate->getId() && count($contentTypes) > 0) {
            return false;
        }
        foreach ($contentTypes as $contentType) {
            if ($contentType->getId() !== $contentTypeToValidate->getId()) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param PageInterface $page
     *
     * @return bool
     */
    public function updateContentForPage(PageInterface $page)
    {
        $contentType = $this->getPageContentType($page);
        if (!$contentType instanceof ContentTypeInterface) {
            $hasRemovedContent = false;
            foreach ($page->getContents() as $content) {
                $this->em->remove($content);
                $hasRemovedContent = true;
            }
            return $hasRemovedContent;
        }

        $hasRemovedContent = false;
        foreach ($page->getContents() as $content) {
            if ($content->getContentType()->getId() !== $contentType->getId()) {
                $this->em->remove($content);
                $hasRemovedContent = true;
            }
        }

        return $hasRemovedContent;
    }

    /**
     * @param PageTypeInterface $pageType
     *
     * @return bool
     */
    public function updatePagesAfterPageTypeRemove(PageTypeInterface $pageType)
    {
        /** @var PageInterface[] $pages */
        $pages = $this->em->getRepository($this->configurationManager->getEntityClass('page'))->findBy([
            'pageType' => $pageType,
        ]);

        if (count($pages) === 0) {
            return false;
        }

        foreach ($pages as $page) {
            $page->setPageType(null);
            $this->updateContentForPage($page);
        }

        return true;
    }

    /**
     * @return bool
     */
    public function updatePages()
    {
        /** @var PageInterface[] $pages */
        $pages = $this->em->getRepository($this->configurationManager->getEntityClass('page'))->findAll();

        $shouldFlush = false;
        foreach ($pages as $page) {
            if ($this->updateContentForPage($page)) {
                $shouldFlush = true;
            }
        }

        return $shouldFlush;
    }

    /**
     * Get page meta by its id
     *
     * @param int $id
     *
     * @return null|PageMetaInterface
     */
    public function getPageMetaById($id)
    {
        return $this->em->getRepository($this->configurationManager->getEntityClass('page_meta'))->find($id);
    }
}
