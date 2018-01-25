<?php

namespace Sherlockode\AdvancedContentBundle\Model;

abstract class FieldValue
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var Content
     */
    protected $content;

    /**
     * @var Field
     */
    protected $field;

    /**
     * @var string
     */
    protected $value;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Content
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param Content $content
     *
     * @return $this
     */
    public function setContent(Content $content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * @return Field
     */
    public function getField()
    {
        return $this->field;
    }

    /**
     * @param Field $field
     *
     * @return $this
     */
    public function setField(Field $field)
    {
        $this->field = $field;

        return $this;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }
}
