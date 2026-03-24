<?php

namespace Sherlockode\AdvancedContentBundle\Model;

interface ContentVersionInterface
{
    /**
     * @return ContentInterface
     */
    public function getContent();

    /**
     * @return $this
     */
    public function setContent(ContentInterface $content);

    /**
     * @return array
     */
    public function getData();

    /**
     * @return $this
     */
    public function setData(array $data);
}
