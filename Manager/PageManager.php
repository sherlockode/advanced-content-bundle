<?php

namespace Sherlockode\AdvancedContentBundle\Manager;

use Doctrine\ORM\EntityManagerInterface;
use Sherlockode\AdvancedContentBundle\Model\PageInterface;
use Sherlockode\AdvancedContentBundle\Model\PageMetaInterface;
use Sherlockode\AdvancedContentBundle\Model\PageTypeInterface;
use Sherlockode\AdvancedContentBundle\Slug\SlugProviderInterface;

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
     * @var SlugProviderInterface
     */
    private $slugProvider;

    /**
     * @param ConfigurationManager   $configurationManager
     * @param EntityManagerInterface $em
     * @param SlugProviderInterface  $slugProvider
     */
    public function __construct(
        ConfigurationManager $configurationManager,
        EntityManagerInterface $em,
        SlugProviderInterface $slugProvider
    ) {
        $this->configurationManager = $configurationManager;
        $this->em = $em;
        $this->slugProvider = $slugProvider;
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
        }

        return true;
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

    /**
     * @param PageInterface $page
     *
     * @return PageInterface
     */
    public function duplicate(PageInterface $page): PageInterface
    {
        $newPage = clone $page;
        $newPage->setPageIdentifier($this->slugProvider->getValidSlug(
            $newPage->getPageIdentifier(),
            $this->configurationManager->getEntityClass('page'),
            'pageIdentifier',
        ));

        foreach ($newPage->getPageMetas() as $pageMeta) {
            $pageMeta->setSlug($this->slugProvider->getValidSlug(
                $pageMeta->getSlug(),
                $this->configurationManager->getEntityClass('page_meta'),
                'slug',
                ['locale' => $pageMeta->getLocale()],
            ));
        }

        foreach ($newPage->getContents() as $content) {
            $content->setSlug($this->slugProvider->getValidSlug(
                $content->getSlug(),
                $this->configurationManager->getEntityClass('content'),
                'slug',
                $content->getLocale() === null ? [] : ['locale' => $content->getLocale()],
            ));
        }

        return $newPage;
    }
}
