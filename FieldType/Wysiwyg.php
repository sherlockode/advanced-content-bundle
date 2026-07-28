<?php

declare(strict_types=1);

namespace Sherlockode\AdvancedContentBundle\FieldType;

use Sherlockode\AdvancedContentBundle\Form\Type\WysiwygType;

class Wysiwyg extends AbstractFieldType
{
    public function getFormFieldType(): string
    {
        return WysiwygType::class;
    }

    #[\Override]
    protected function getDefaultIconClass(): string
    {
        return 'fa-solid fa-text-height';
    }

    /**
     * Get field's code.
     */
    public function getCode(): string
    {
        return 'wysiwyg';
    }

    public function getPreviewPicture(): ?string
    {
        return 'bundles/sherlockodeadvancedcontent/preview_picture/wysiwyg.svg';
    }
}
