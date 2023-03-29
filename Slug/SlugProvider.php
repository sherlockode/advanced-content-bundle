<?php

namespace Sherlockode\AdvancedContentBundle\Slug;

use Sherlockode\AdvancedContentBundle\Model\ContentInterface;
use Sherlockode\AdvancedContentBundle\Model\PageInterface;
use Sherlockode\AdvancedContentBundle\Scope\ScopeHandlerInterface;

class SlugProvider implements SlugProviderInterface
{
    /**
     * @var ScopeHandlerInterface
     */
    private $scopeHandler;

    /**
     * @param ScopeHandlerInterface $scopeHandler
     */
    public function __construct(ScopeHandlerInterface $scopeHandler)
    {
        $this->scopeHandler = $scopeHandler;
    }

    /**
     * @param PageInterface $page
     */
    public function setPageValidIdentifier(PageInterface $page): void
    {
        while (true) {
            if ($this->scopeHandler->isPageIdentifierValid($page)) {
                break;
            }
            $page->setPageIdentifier($this->getNewValue($page->getPageIdentifier()));
        }
    }

    /**
     * @param PageInterface $page
     */
    public function setPageValidSlug(PageInterface $page): void
    {
        while (true) {
            if ($this->scopeHandler->isPageSlugValid($page)) {
                break;
            }
            $page->getPageMeta()->setSlug($this->getNewValue($page->getPageMeta()->getSlug()));
        }
    }

    /**
     * @param ContentInterface $content
     */
    public function setContentValidSlug(ContentInterface $content): void
    {
        while (true) {
            if ($this->scopeHandler->isContentSlugValid($content)) {
                break;
            }
            $content->setSlug($this->getNewValue($content->getSlug()));
        }
    }

    /**
     * @param string $value
     *
     * @return string
     */
    private function getNewValue($value): string
    {
        if (preg_match('/-(\d+)$/', $value, $matches) && array_key_exists(1, $matches)) {
            return preg_replace('/' . $matches[1] . '$/', $matches[1] + 1, $value);
        }

        return $value . '-1';
    }
}
