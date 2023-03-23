<?php

namespace Sherlockode\AdvancedContentBundle\Model;

abstract class ContentVersion extends Version implements ContentVersionInterface
{
    /**
     * @var ContentInterface
     */
    protected $content;

    /**
     * @var array
     */
    protected $data;

    /**
     * ContentVersion constructor
     */
    public function __construct()
    {
        $this->data = [];
        parent::__construct();
    }

    /**
     * @return ContentInterface
     */
    public function getContent(): ContentInterface
    {
        return $this->content;
    }

    /**
     * @param ContentInterface $content
     *
     * @return $this
     */
    public function setContent(ContentInterface $content): self
    {
        $this->content = $content;

        return $this;
    }

    /**
     * @return array
     */
    public function getData()
    {
        $data = $this->data ?? [];
        uasort($data, function ($a, $b) {
            return ($a['position'] ?? 0) <=> ($b['position'] ?? 0);
        });

        return $data;
    }

    /**
     * @param array $data
     *
     * @return $this
     */
    public function setData(array $data)
    {
        $this->data = $data;

        return $this;
    }
}
