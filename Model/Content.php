<?php

namespace AdvancedContentBundle\Model;

class Content
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var int
     */
    private $contentTypeId;

    /**
     * @var array
     */
    private $fieldValues;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getContentTypeId()
    {
        return $this->contentTypeId;
    }

    /**
     * @param int $contentTypeId
     *
     * @return $this
     */
    public function setContentTypeId($contentTypeId)
    {
        $this->contentTypeId = $contentTypeId;

        return $this;
    }

    /**
     * @return array
     */
    public function getFieldValues()
    {
        return $this->fieldValues;
    }

    /**
     * @param array $fieldValues
     *
     * @return $this
     */
    public function setFieldValues($fieldValues)
    {
        $this->fieldValues = $fieldValues;

        return $this;
    }
}
