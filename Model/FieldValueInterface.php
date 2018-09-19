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
     * Get field value's field
     *
     * @return FieldInterface
     */
    public function getField();

    /**
     * Set field value's field
     *
     * @param FieldInterface $field
     *
     * @return $this
     */
    public function setField(FieldInterface $field);

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
