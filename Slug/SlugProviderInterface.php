<?php

namespace Sherlockode\AdvancedContentBundle\Slug;

use Sherlockode\AdvancedContentBundle\Model\ContentInterface;
use Sherlockode\AdvancedContentBundle\Model\PageInterface;

interface SlugProviderInterface
{
    /**
     * @param PageInterface $page
     */
    public function setPageValidIdentifier(PageInterface $page): void;

    /**
     * @param PageInterface $page
     */
    public function setPageValidSlug(PageInterface $page): void;

    /**
     * @param ContentInterface $content
     */
    public function setContentValidSlug(ContentInterface $content): void;
}
