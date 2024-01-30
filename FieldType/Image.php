<?php

namespace Sherlockode\AdvancedContentBundle\FieldType;

use Sherlockode\AdvancedContentBundle\Form\Type\PictureType;

class Image extends File
{
    /**
     * @return string
     */
    public function getFormFieldType()
    {
        return PictureType::class;
    }

    protected function getDefaultIconClass()
    {
        return 'fa-solid fa-image';
    }

    /**
     * Get field's code
     *
     * @return string
     */
    public function getCode()
    {
        return 'image';
    }

    public function getRawValue($element)
    {
        $element['image'] = parent::getRawValue($element['image'] ?? []);
        $element = array_merge($element, $element['image']);
        if (isset($element['sources']) && is_array($element['sources'])) {
            foreach ($element['sources'] as $key => $source) {
                $element['sources'][$key] = parent::getRawValue($source);
            }
        }

        return $element;
    }
}
