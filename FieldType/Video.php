<?php

declare(strict_types=1);

namespace Sherlockode\AdvancedContentBundle\FieldType;

use Sherlockode\AdvancedContentBundle\Form\Type\VideoType;

class Video extends AbstractFieldType
{
    /**
     * @return string
     */
    public function getFormFieldType()
    {
        return VideoType::class;
    }

    protected function getDefaultIconClass()
    {
        return 'fa-solid fa-video';
    }

    /**
     * Get field's code
     *
     * @return string
     */
    public function getCode()
    {
        return 'video';
    }

    public function getPreviewPicture(): ?string
    {
        return 'bundles/sherlockodeadvancedcontent/preview_picture/video.svg';
    }
}
