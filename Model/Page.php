<?php

namespace Sherlockode\AdvancedContentBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

abstract class Page implements PageInterface, ScopableInterface
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
     * @var PageTypeInterface
     */
    protected $pageType;

    /**
     * @var PageMetaInterface
     */
    protected $pageMeta;

    /**
     * @var Collection
     */
    protected $scopes;

    /**
     * @var PageVersionInterface
     */
    protected $pageVersion;

    /**
     * @var Collection|PageVersionInterface[]
     */
    protected $versions;

    public function __construct()
    {
        $this->scopes = new ArrayCollection();
        $this->versions = new ArrayCollection();
    }

    public function __clone()
    {
        $this->id = null;
        $this->setStatus(PageInterface::STATUS_DRAFT);

        $newPageMeta = clone $this->pageMeta;
        $newPageMeta->setPage($this);
        $this->pageMeta = $newPageMeta;

        $newContent = clone $this->content;
        $newContent->setPage($this);
        $this->content = $newContent;
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
     * @return ContentInterface|null
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param ContentInterface|null $content
     *
     * @return PageInterface|void
     */
    public function setContent(ContentInterface $content = null)
    {
        if ($content !== null) {
            $content->setPage($this);
        }
        $this->content = $content;

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
     * @return PageMetaInterface|null
     */
    public function getPageMeta()
    {
        return $this->pageMeta;
    }

    /**
     * @param PageMetaInterface|null $pageMeta
     *
     * @return PageInterface|void
     */
    public function setPageMeta(PageMetaInterface $pageMeta = null)
    {
        if ($pageMeta !== null) {
            $pageMeta->setPage($this);
        }
        $this->pageMeta = $pageMeta;

        return $this;
    }

    /**
     * @return ArrayCollection|Collection|ScopeInterface[]
     */
    public function getScopes()
    {
        return $this->scopes;
    }

    /**
     * @param ScopeInterface $scope
     *
     * @return $this
     */
    public function addScope(ScopeInterface $scope)
    {
        $this->scopes->add($scope);

        return $this;
    }

    /**
     * @param ScopeInterface $scope
     *
     * @return $this
     */
    public function removeScope(ScopeInterface $scope)
    {
        $this->scopes->removeElement($scope);

        return $this;
    }

    /**
     * @return PageVersionInterface|null
     */
    public function getPageVersion(): ?PageVersionInterface
    {
        return $this->pageVersion;
    }

    /**
     * @param PageVersionInterface|null $pageVersion
     *
     * @return $this
     */
    public function setPageVersion(?PageVersionInterface $pageVersion)
    {
        $this->pageVersion = $pageVersion;

        return $this;
    }

    /**
     * @return ArrayCollection|Collection|PageVersionInterface[]
     */
    public function getVersions()
    {
        return $this->versions;
    }

    /**
     * @param PageVersionInterface $pageVersion
     *
     * @return $this
     */
    public function addVersion(PageVersionInterface $pageVersion)
    {
        $pageVersion->setPage($this);
        $this->versions->add($pageVersion);

        return $this;
    }

    /**
     * @param PageVersionInterface $pageVersion
     *
     * @return $this
     */
    public function removeVersion(PageVersionInterface $pageVersion)
    {
        $this->versions->removeElement($pageVersion);

        return $this;
    }
}
