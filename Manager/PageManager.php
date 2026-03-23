<?php

declare(strict_types=1);

namespace Sherlockode\AdvancedContentBundle\Manager;

use Sherlockode\AdvancedContentBundle\Model\PageInterface;
use Sherlockode\AdvancedContentBundle\Slug\SlugProviderInterface;

class PageManager
{
    public function __construct(private readonly SlugProviderInterface $slugProvider)
    {
    }

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
