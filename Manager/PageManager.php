<?php

namespace Sherlockode\AdvancedContentBundle\Manager;

use Doctrine\ORM\EntityManagerInterface;
use Sherlockode\AdvancedContentBundle\Model\PageInterface;
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
     * @param PageInterface $page
     *
     * @return PageInterface
     */
    public function duplicate(PageInterface $page): PageInterface
    {
        $newPage = clone $page;
        $this->slugProvider->setPageValidIdentifier($newPage);

        $pageMeta = $newPage->getPageMeta();
        if ($pageMeta !== null) {
            $this->slugProvider->setPageValidSlug($newPage);
        }

        $content = $newPage->getContent();
        if ($content !== null) {
            $this->slugProvider->setContentValidSlug($content);
        }

        return $newPage;
    }
}
