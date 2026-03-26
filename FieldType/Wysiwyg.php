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

    #[\Override]
    protected function getDefaultIconClass()
    {
        return 'fa-solid fa-text-height';
    }

    /**
     * Get field's code.
     *
     * @return string
     */
    public function getCode()
    {
        return 'wysiwyg';
    }

    public function getPreviewPicture(): ?string
    {
        return 'bundles/sherlockodeadvancedcontent/preview_picture/wysiwyg.svg';
    }
}
