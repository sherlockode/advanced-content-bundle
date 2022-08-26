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
     * @return string
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
     * @return FieldValueInterface
     */
    public function getGroup();

    /**
     * @param FieldGroupValueInterface|null $parent
     *
     * @return $this
     */
    public function setGroup(FieldGroupValueInterface $parent = null);

    /**
     * @return FieldGroupValueInterface[]
     */
    public function getChildren();
}
