<?php

namespace Sherlockode\AdvancedContentBundle\FieldType;

use Sherlockode\AdvancedContentBundle\Form\Type\WysiwygType;

class Wysiwyg extends AbstractFieldType
{
    /**
     * @return string
     */
    public function getFormFieldType()
    {
        return WysiwygType::class;
    }

    protected function getDefaultIconClass()
    {
        return 'fa-solid fa-text-height';
    }

    /**
     * Get field's code
     *
     * @return string
     */
    public function getCode()
    {
        return 'wysiwyg';
    }
}
