<?php

declare(strict_types=1);

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
    protected $data = [];

    /**
     * ContentVersion constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function getContent(): ContentInterface
    {
        return $this->content;
    }

    /**
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
        uasort($data, fn ($a, $b): int => ($a['position'] ?? 0) <=> ($b['position'] ?? 0));

        return $data;
    }

    /**
     * @return $this
     */
    public function setData(array $data)
    {
        $this->data = $data;

        return $this;
    }
}
