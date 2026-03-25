<?php

namespace Sherlockode\AdvancedContentBundle\Slug;

use Sherlockode\AdvancedContentBundle\Model\ContentInterface;
use Sherlockode\AdvancedContentBundle\Model\PageInterface;

interface SlugProviderInterface
{
    public function setPageValidIdentifier(PageInterface $page): void;

    public function setPageValidSlug(PageInterface $page): void;

    public function setContentValidSlug(ContentInterface $content): void;
}
