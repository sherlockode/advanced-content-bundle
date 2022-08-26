<?php

namespace Sherlockode\AdvancedContentBundle\Model;

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
     * Get content's field values
     *
     * @return Collection|FieldValueInterface[]
     */
    public function getFieldValues();

    /**
     * Add field value to content
     *
     * @param FieldValueInterface $fieldValue
     *
     * @return $this
     */
    public function addFieldValue(FieldValueInterface $fieldValue);

    /**
     * Remove field value from content
     *
     * @param FieldValueInterface $fieldValue
     *
     * @return $this
     */
    public function removeFieldValue(FieldValueInterface $fieldValue);

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
}
