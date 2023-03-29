<?php

namespace Sherlockode\AdvancedContentBundle\Manager;

use Sherlockode\AdvancedContentBundle\Model\PageInterface;
use Sherlockode\AdvancedContentBundle\Slug\SlugProviderInterface;

class PageManager
{
    /**
     * @var SlugProviderInterface
     */
    private $slugProvider;

    /**
     * @param SlugProviderInterface $slugProvider
     */
    public function __construct(
        SlugProviderInterface $slugProvider
    ) {
        $this->slugProvider = $slugProvider;
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
