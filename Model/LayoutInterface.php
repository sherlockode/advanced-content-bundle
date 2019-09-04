<?php

namespace Sherlockode\AdvancedContentBundle\Model;

interface LayoutInterface
{
    /**
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
     * @return FieldInterface
     */
    public function getParent();

    /**
     * @param FieldInterface|null $parent
     *
     * @return $this
     */
    public function setParent(FieldInterface $parent = null);

    /**
     * @return FieldInterface[]
     */
    public function getChildren();

    /**
     * @param FieldInterface $child
     *
     * @return $this
     */
    public function addChild(FieldInterface $child);

    /**
     * @param FieldInterface $child
     *
     * @return $this
     */
    public function removeChild(FieldInterface $child);

    /**
     * @return FieldGroupValueInterface[]
     */
    public function getFieldGroupValues();

    /**
     * @param FieldGroupValueInterface $fieldGroupValue
     *
     * @return $this
     */
    public function addFieldGroupValue(FieldGroupValueInterface $fieldGroupValue);

    /**
     * @param FieldGroupValueInterface $fieldGroupValue
     *
     * @return $this
     */
    public function removeFieldGroupValue(FieldGroupValueInterface $fieldGroupValue);

    /**
     * Get layout's sort order
     *
     * @return int
     */
    public function getPosition();

    /**
     * Set layout's sort order
     *
     * @param int $position
     *
     * @return $this
     */
    public function setPosition($position);
}
