<?php

namespace Sherlockode\AdvancedContentBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

interface ContentInterface
{
    /**
     * Get content id
     *
     * @return int
     */
    public function getId();

    /**
     * @return string
     */
    public function getName();

    /**
     * @param string $name
     *
     * @return $this
     */
    public function setName($name);

    /**
     * Get content's slug
     *
     * @return string
     */
    public function getSlug();

    /**
     * Set content's slug
     *
     * @param string $slug
     *
     * @return $this
     */
    public function setSlug($slug);

    /**
     * @return array
     */
    public function getData();

    /**
     * @param array $data
     * @param bool  $resetContentVersion
     *
     * @return $this
     */
    public function setData(array $data, bool $resetContentVersion = true);

    /**
     * Get content's content page
     *
     * @return PageInterface|null
     */
    public function getPage();

    /**
     * @param PageInterface|null $page
     *
     * @return $this
     */
    public function setPage(PageInterface $page = null);

    /**
     * @return string|null
     */
    public function getLocale();

    /**
     * @param string $locale
     *
     * @return $this
     */
    public function setLocale($locale);

    /**
     * @return ContentVersionInterface|null
     */
    public function getContentVersion(): ?ContentVersionInterface;

    /**
     * @param ContentVersionInterface|null $contentVersion
     *
     * @return $this
     */
    public function setContentVersion(?ContentVersionInterface $contentVersion);

    /**
     * @return ArrayCollection|Collection
     */
    public function getVersions();
}
