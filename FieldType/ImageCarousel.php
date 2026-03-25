<?php

namespace Sherlockode\AdvancedContentBundle\FieldType;

use Sherlockode\AdvancedContentBundle\Form\Type\ImageCarouselType;

class ImageCarousel extends File
{
    /**
     * @return string
     */
    #[\Override]
    public function getFormFieldType()
    {
        return ImageCarouselType::class;
    }

    #[\Override]
    protected function getDefaultIconClass()
    {
        return 'fa-solid fa-images';
    }

    /**
     * Get field's code.
     *
     * @return string
     */
    #[\Override]
    public function getCode()
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
