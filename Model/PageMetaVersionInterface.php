<?php

namespace Sherlockode\AdvancedContentBundle\Model;

interface PageMetaVersionInterface
{
    /**
     * @return PageMetaInterface
     */
    public function getPageMeta();

    /**
     * @param PageMetaInterface $pageMeta
     *
     * @return $this
     */
    public function setPageMeta(PageMetaInterface $pageMeta);

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
}
