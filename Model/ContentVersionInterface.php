<?php

namespace Sherlockode\AdvancedContentBundle\Model;

interface ContentVersionInterface
{
    /**
     * @return ContentInterface
     */
    public function getContent();

    /**
     * @param ContentInterface $content
     *
     * @return $this
     */
    public function setContent(ContentInterface $content);

    /**
     * @return array
     */
    public function getData();

    /**
     * @param array $data
     *
     * @return $this
     */
    public function setData(array $data);
}
