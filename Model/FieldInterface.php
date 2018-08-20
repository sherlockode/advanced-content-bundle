<?php

namespace Sherlockode\AdvancedContentBundle\Model;

interface FieldInterface
{
    /**
     * Get field id
     *
     * @return int
     */
    public function getId();

    /**
     * Get field's type
     *
     * @return string
     */
    public function getType();

    /**
     * Set field's type
     *
     * @param string $type
     *
     * @return $this
     */
    public function setType($type);

    /**
     * Get field's name
     *
     * @return string
     */
    public function getName();

    /**
     * Set field's name
     *
     * @param string $name
     *
     * @return $this
     */
    public function setName($name);

    /**
     * Get field's slug
     *
     * @return string
     */
    public function getSlug();

    /**
     * Set field's slug
     *
     * @param string $slug
     *
     * @return $this
     */
    public function setSlug($slug);

    /**
     * Check if field is required
     *
     * @return bool
     */
    public function isRequired();

    /**
     * Set field's requiredness
     *
     * @param bool $required
     *
     * @return $this
     */
    public function setRequired($required);

    /**
     * Get field's options
     *
     * @return array
     */
    public function getOptions();

    /**
     * Set field's options
     *
     * @param array $options
     *
     * @return $this
     */
    public function setOptions($options);

    /**
     * Get field's content type
     *
     * @return ContentTypeInterface
     */
    public function getContentType();

    /**
     * Set field's content type
     *
     * @param ContentTypeInterface $contentType
     *
     * @return $this
     */
    public function setContentType(ContentTypeInterface $contentType);

    /**
     * Get field's sort order
     *
     * @return int
     */
    public function getSortOrder();

    /**
     * Set field's sort order
     *
     * @param int $sortOrder
     *
     * @return $this
     */
    public function setSortOrder($sortOrder);

    /**
     * Get field's hint
     *
     * @return string
     */
    public function getHint();

    /**
     * Set field's hint
     *
     * @param string $hint
     *
     * @return $this
     */
    public function setHint($hint);
}
