<?php

namespace Sherlockode\AdvancedContentBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

abstract class Page implements PageInterface
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $pageIdentifier;

    /**
     * @var integer
     */
    protected $status;

    /**
     * @var ContentInterface
     */
    protected $content;

    /**
     * @var Collection|ContentInterface[]
     */
    protected $contents;

    /**
     * @var PageTypeInterface
     */
    protected $pageType;

    /**
     * Non-mapped property, stores the current locale to use when displaying the content
     *
     * @var string
     */
    protected $currentLocale;

    /**
     * @var Collection|PageMetaInterface[]
     */
    protected $pageMetas;

    public function __construct()
    {
        $this->contents = new ArrayCollection();
        $this->pageMetas = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getPageIdentifier()
    {
        return $this->pageIdentifier;
    }

    /**
     * @param string $pageIdentifier
     *
     * @return $this
     */
    public function setPageIdentifier($pageIdentifier)
    {
        $this->pageIdentifier = $pageIdentifier;

        return $this;
    }

    /**
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param int $status
     *
     * @return $this
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @param string|null $locale
     *
     * @return ContentInterface|null
     */
    public function getContent($locale = null)
    {
        $locale = $locale ?: $this->currentLocale;
        foreach ($this->contents as $content) {
            if ($content->getLocale() === $locale) {
                return $content;
            }
        }

        return $this->contents[0] ?? null;
    }

    /**
     * @param ContentInterface|null $content
     *
     * @return PageInterface|void
     */
    public function setContent(ContentInterface $content = null)
    {
        $this->contents->clear();
        $content->setPage($this);
        $this->contents->add($content);
    }

    public function getContents()
    {
        return $this->contents;
    }

    /**
     * @param ContentInterface $content
     *
     * @return $this
     */
    public function addContent(ContentInterface $content)
    {
        $this->contents[] = $content;
        $content->setPage($this);

        return $this;
    }

    /**
     * @param ContentInterface $content
     *
     * @return $this
     */
    public function removeContent(ContentInterface $content)
    {
        $this->contents->removeElement($content);

        return $this;
    }

    /**
     * @return PageTypeInterface|null
     */
    public function getPageType()
    {
        return $this->pageType;
    }

    /**
     * @param PageTypeInterface|null $pageType
     *
     * @return $this
     */
    public function setPageType(PageTypeInterface $pageType = null)
    {
        $this->pageType = $pageType;

        return $this;
    }

    /**
     * @param string $locale
     *
     * @return $this
     */
    public function setCurrentLocale($locale)
    {
        $this->currentLocale = $locale;

        return $this;
    }

    /**
     * @return Collection|PageMetaInterface[]
     */
    public function getPageMetas()
    {
        return $this->pageMetas;
    }

    /**
     * @param PageMetaInterface $pageMeta
     *
     * @return $this
     */
    public function addPageMeta(PageMetaInterface $pageMeta)
    {
        $this->pageMetas[] = $pageMeta;
        $pageMeta->setPage($this);

        return $this;
    }

    /**
     * @param PageMetaInterface $pageMeta
     *
     * @return $this
     */
    public function removePageMeta(PageMetaInterface $pageMeta)
    {
        $this->pageMetas->removeElement($pageMeta);

        return $this;
    }

    /**
     * @param string|null $locale
     *
     * @return PageMetaInterface|null
     */
    public function getPageMeta($locale = null)
    {
        $locale = $locale ?: $this->currentLocale;
        foreach ($this->pageMetas as $pageMeta) {
            if ($pageMeta->getLocale() === $locale) {
                return $pageMeta;
            }
        }

        return $this->pageMetas[0] ?? null;
    }

    /**
     * @param PageMetaInterface|null $pageMeta
     *
     * @return PageInterface|void
     */
    public function setPageMeta(PageMetaInterface $pageMeta = null)
    {
        $this->pageMetas->clear();
        if ($pageMeta !== null) {
            $pageMeta->setPage($this);
        }
        $this->pageMetas->add($pageMeta);
    }
}
