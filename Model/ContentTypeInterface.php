<?php

namespace Sherlockode\AdvancedContentBundle\Model;

interface ContentTypeInterface
{
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
     * @return \Traversable
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
}
