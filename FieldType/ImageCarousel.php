<?php

declare(strict_types=1);

namespace Sherlockode\AdvancedContentBundle\FieldType;

use Sherlockode\AdvancedContentBundle\Form\Type\ImageCarouselType;

class ImageCarousel extends File
{
    #[\Override]
    public function getFormFieldType(): string
    {
        return ImageCarouselType::class;
    }

    #[\Override]
    protected function getDefaultIconClass(): string
    {
        return 'fa-solid fa-images';
    }

    /**
     * Get field's code.
     */
    #[\Override]
    public function getCode(): string
    {
        return 'image_carousel';
    }

    #[\Override]
    public function getPreviewPicture(): ?string
    {
        return 'bundles/sherlockodeadvancedcontent/preview_picture/image_carousel.svg';
    }

    #[\Override]
    public function getRawValue($element)
    {
        $images = $element['images'] ?? [];
        foreach ($images as $key => $imageData) {
            $url = $this->getFilename($imageData);
            if (empty($url)) {
                unset($element['images'][$key]);
                continue;
            }

            $imageData['url'] = $url;
            if (isset($imageData['delete'])) {
                unset($imageData['delete']);
            }

            $element['images'][$key] = $imageData;
        }

        return $element;
    }
}
