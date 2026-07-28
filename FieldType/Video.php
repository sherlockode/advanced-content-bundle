<?php

declare(strict_types=1);

namespace Sherlockode\AdvancedContentBundle\FieldType;

use Sherlockode\AdvancedContentBundle\Form\Type\VideoType;

class Video extends AbstractFieldType
{
    public function getFormFieldType(): string
    {
        return VideoType::class;
    }

    #[\Override]
    protected function getDefaultIconClass(): string
    {
        return 'fa-solid fa-video';
    }

    /**
     * Get field's code.
     */
    public function getCode(): string
    {
        return 'video';
    }

    public function getPreviewPicture(): ?string
    {
        return 'bundles/sherlockodeadvancedcontent/preview_picture/video.svg';
    }
}
