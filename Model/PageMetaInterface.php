<?php

declare(strict_types=1);

namespace Sherlockode\AdvancedContentBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

interface PageMetaInterface
{
    /**
     * @return int
     */
    public function getId();

    /**
     * @return string
     */
    public function getTitle();

    /**
     * @param string $title
     *
     * @return $this
     */
    public function setTitle($title);

    /**
     * @return string
     */
    public function getSlug();

    /**
     * @param string $slug
     *
     * @return $this
     */
    public function setSlug($slug);

    /**
     * @return string
     */
    public function getMetaTitle();

    /**
     * @param string $metaTitle
     *
     * @return $this
     */
    public function setMetaTitle($metaTitle);

    /**
     * @return string
     */
    public function getMetaDescription();

    /**
     * @param string $metaDescription
     *
     * @return $this
     */
    public function setMetaDescription($metaDescription);

    /**
     * @return PageInterface|null
     */
    public function getPage();

    /**
     * @return $this
     */
    public function setPage(PageInterface $page);

    /**
     * @return ArrayCollection|Collection|PageMetaVersionInterface[]
     */
    public function getVersions();
}
