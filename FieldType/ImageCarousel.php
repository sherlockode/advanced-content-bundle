<?php

namespace Sherlockode\AdvancedContentBundle\FieldType;

use Sherlockode\AdvancedContentBundle\Form\Type\ImageCarouselType;

class ImageCarousel extends File
{
    /**
     * @return string
     */
    public function getFormFieldType()
    {
        return ImageCarouselType::class;
    }

    protected function getDefaultIconClass()
    {
        return 'fa-solid fa-images';
    }

    /**
     * Get field's code
     *
     * @return string
     */
    public function getCode()
    {
        return 'image_carousel';
    }

    /**
     * @param mixed $element
     *
     * @return mixed
     */
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
