<?php

namespace Sherlockode\AdvancedContentBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

abstract class Content implements ContentInterface, ScopableInterface
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $slug;

    /**
     * @var array
     */
    protected $data;

    /**
     * @var PageInterface
     */
    protected $page;

    /**
     * @var ContentVersionInterface|null
     */
    protected $contentVersion;

    /**
     * @var Collection
     */
    protected $versions;

    /**
     * @var Collection
     */
    protected $scopes;

    /**
     * Content constructor
     */
    public function __construct()
    {
        $this->data = [];
        $this->versions = new ArrayCollection();
        $this->scopes = new ArrayCollection();
    }

    public function __clone()
    {
        $this->id = null;
        $this->versions = new ArrayCollection();
        $this->contentVersion = null;
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
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;

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
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param array $data
     * @param bool  $resetContentVersion
     *
     * @return $this
     */
    public function setData(array $data, bool $resetContentVersion = true)
    {
        $this->data = $data;
        if ($resetContentVersion) {
            $this->contentVersion = null;
        }

        return $this;
    }

    /**
     * @return PageInterface|null
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * @param PageInterface $page
     *
     * @return $this
     */
    public function setPage(PageInterface $page = null)
    {
        $this->page = $page;

        return $this;
    }

    /**
     * @return ContentVersionInterface|null
     */
    public function getContentVersion(): ?ContentVersionInterface
    {
        return $this->contentVersion;
    }

    /**
     * @param ContentVersionInterface|null $contentVersion
     *
     * @return $this
     */
    public function setContentVersion(?ContentVersionInterface $contentVersion)
    {
        $this->contentVersion = $contentVersion;

        return $this;
    }

    /**
     * @return ArrayCollection|Collection
     */
    public function getVersions()
    {
        return $this->versions;
    }

    /**
     * @param ContentVersionInterface $contentVersion
     *
     * @return $this
     */
    public function addVersion(ContentVersionInterface $contentVersion)
    {
        $contentVersion->setContent($this);
        $this->versions->add($contentVersion);

        return $this;
    }

    /**
     * @param ContentVersionInterface $contentVersion
     *
     * @return $this
     */
    public function removeVersion(ContentVersionInterface $contentVersion)
    {
        $this->versions->removeElement($contentVersion);

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
}
