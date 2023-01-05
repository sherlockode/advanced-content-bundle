<?php

namespace Sherlockode\AdvancedContentBundle\FieldType;

use Sherlockode\AdvancedContentBundle\Form\Type\ImageCarouselType;
use Sherlockode\AdvancedContentBundle\Model\FieldValueInterface;

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
     * @param FieldValueInterface $fieldValue
     *
     * @return mixed
     */
    public function getRawValue(FieldValueInterface $fieldValue)
    {
        $value = $fieldValue->getValue();
        $images = $value['images'] ?? [];
        foreach ($images as $key => $imageData) {
            $url = $this->getFilename($imageData);
            if (empty($url)) {
                unset($value['images'][$key]);
                continue;
            }

            $imageData['url'] = $url;
            if (isset($imageData['delete'])) {
                unset($imageData['delete']);
            }
            $value['images'][$key] = $imageData;
        }

        return $value;
    }
}
