<?php

namespace Sherlockode\AdvancedContentBundle\Model;

interface FieldValueInterface
{
    /**
     * Get field value's id
     *
     * @return int
     */
    public function getId();

    /**
     * Get field value's content
     *
     * @return ContentInterface
     */
    public function getContent();

    /**
     * Set field value's content
     *
     * @param ContentInterface $content
     *
     * @return $this
     */
    public function setContent(ContentInterface $content);

    /**
     * @return string
     */
    public function getFieldType();

    /**
     * @param string $fieldType
     *
     * @return $this
     */
    public function setFieldType(string $fieldType);

    /**
     * Get field value's value
     *
     * @return mixed
     */
    public function getValue();

    /**
     * Set field value's value
     *
     * @param string $value
     *
     * @return $this
     */
    public function setValue($value);

    /**
     * @return int
     */
    public function getPosition();

    /**
     * @param int $position
     *
     * @return $this
     */
    public function setPosition($position);
}
