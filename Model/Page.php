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
    protected $title;

    /**
     * @var string
     */
    protected $slug;

    /**
     * @var string
     */
    protected $metaDescription;

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

    public function __construct()
    {
        $this->contents = new ArrayCollection();
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
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     *
     * @return $this
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * @param string $slug
     *
     * @return $this
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * @return string
     */
    public function getMetaDescription()
    {
        return $this->metaDescription;
    }

    /**
     * @param string $metaDescription
     *
     * @return $this
     */
    public function setMetaDescription($metaDescription)
    {
        $this->metaDescription = $metaDescription;

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
}
