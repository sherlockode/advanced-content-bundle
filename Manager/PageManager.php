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

    public function __construct(
        SlugProviderInterface $slugProvider,
    ) {
        $this->slugProvider = $slugProvider;
    }

    public function duplicate(PageInterface $page): PageInterface
    {
        $newPage = clone $page;
        $this->slugProvider->setPageValidIdentifier($newPage);

        $pageMeta = $newPage->getPageMeta();
        if (null !== $pageMeta) {
            $this->slugProvider->setPageValidSlug($newPage);
        }

        $content = $newPage->getContent();
        if (null !== $content) {
            $this->slugProvider->setContentValidSlug($content);
        }

        return $newPage;
    }
}
