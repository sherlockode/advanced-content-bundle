<?php

namespace Sherlockode\AdvancedContentBundle\Manager;

use Sherlockode\AdvancedContentBundle\Model\ContentInterface;
use Sherlockode\AdvancedContentBundle\Model\FieldInterface;

class ContentManager
{
    /**
     * Get field matching slug
     *
     * @param ContentInterface $content
     * @param string           $slug
     *
     * @return FieldInterface|null
     */
    public function getFieldBySlug(ContentInterface $content, $slug)
    {
        $fields = $content->getContentType()->getFields();
        foreach ($fields as $field) {
            if ($field->getSlug() == $slug) {
                return $field;
            }
        }

        return null;
    }
}
