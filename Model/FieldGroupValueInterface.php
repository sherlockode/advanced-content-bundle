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
    public function setParent(FieldValueInterface $fieldValue);

    /**
     * @return FieldValueInterface[]
     */
    public function getChildren();
}
