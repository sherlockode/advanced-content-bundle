<?php

namespace Sherlockode\AdvancedContentBundle\Manager;

use Sherlockode\AdvancedContentBundle\Model\Content;
use Sherlockode\AdvancedContentBundle\Model\Field;

class ContentManager
{
    /**
     * Get field matching slug
     *
     * @param Content $content
     * @param string  $slug
     *
     * @return Field|null
     */
    public function getFieldBySlug(Content $content, $slug)
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
