<?php

namespace Sherlockode\AdvancedContentBundle\Model;

interface FieldGroupValueInterface
{
    /**
     * @return int
     */
    public function getId();

    /**
     * @return FieldValueInterface
     */
    public function getParent();

    /**
     * @param FieldValueInterface $fieldValue
     *
     * @return $this
     */
    public function setParent(FieldValueInterface $fieldValue = null);

    /**
     * @return FieldValueInterface[]
     */
    public function getChildren();

    /**
     * @return LayoutInterface
     */
    public function getLayout();

    /**
     * @param LayoutInterface $layout
     *
     * @return $this
     */
    public function setLayout(LayoutInterface $layout = null);

    /**
     * Get field group's sort order
     *
     * @return int
     */
    public function getPosition();

    /**
     * Set field group's sort order
     *
     * @param int $position
     *
     * @return $this
     */
    public function setPosition($position);
}
