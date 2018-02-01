<?php

namespace Sherlockode\AdvancedContentBundle\Model;

abstract class FieldValue implements FieldValueInterface
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var ContentInterface
     */
    protected $content;

    /**
     * @var FieldInterface
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
     * @return ContentInterface
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param ContentInterface $content
     *
     * @return $this
     */
    public function setContent(ContentInterface $content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * @return FieldInterface
     */
    public function getField()
    {
        return $this->field;
    }

    /**
     * @param FieldInterface $field
     *
     * @return $this
     */
    public function setField(FieldInterface $field)
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
