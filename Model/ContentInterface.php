<?php

namespace Sherlockode\AdvancedContentBundle\Model;

interface ContentInterface
{
    /**
     * Get content id
     *
     * @return int
     */
    public function getId();

    /**
     * Get content's content type
     *
     * @return ContentTypeInterface
     */
    public function getContentType();

    /**
     * Set content's content type
     *
     * @param ContentTypeInterface $contentType
     *
     * @return $this
     */
    public function setContentType(ContentTypeInterface $contentType);

    /**
     * Get content's field values
     *
     * @return \Traversable
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
}