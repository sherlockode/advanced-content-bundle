<?php

namespace Sherlockode\AdvancedContentBundle\Model;

interface ContentTypeInterface
{
    const LINK_TYPE_NO_LINK = 0;
    const LINK_TYPE_PAGE_TYPE = 1;
    const LINK_TYPE_PAGE = 2;

    /**
     * Get content type id
     *
     * @return int
     */
    public function getId();

    /**
     * Get content type's name
     *
     * @return string
     */
    public function getName();

    /**
     * Set content type's name
     *
     * @param string $name
     *
     * @return $this
     */
    public function setName($name);

    /**
     * Get content type's list of fields
     *
     * @return Field[]
     */
    public function getFields();

    /**
     * Add a field to content type
     *
     * @param FieldInterface $field
     *
     * @return $this
     */
    public function addField(FieldInterface $field);

    /**
     * Remove field from content type
     *
     * @param FieldInterface $field
     *
     * @return $this
     */
    public function removeField(FieldInterface $field);

    /**
     * @return PageTypeInterface|null
     */
    public function getPageType();

    /**
     * @param PageTypeInterface|null $pageType
     *
     * @return $this
     */
    public function setPageType(PageTypeInterface $pageType = null);

    /**
     * @return PageInterface
     */
    public function getPage();

    /**
     * @param PageInterface|null $page
     *
     * @return $this
     */
    public function setPage(PageInterface $page = null);

    /**
     * @return ContentInterface[]
     */
    public function getContents();

    /**
     * @return ContentInterface|null
     */
    public function getFirstContent();
}
