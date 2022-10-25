<?php

namespace Sherlockode\AdvancedContentBundle\FieldType;

use Sherlockode\AdvancedContentBundle\Form\Type\ImageType;

class Image extends File
{
    /**
     * @return string
     */
    public function getFormFieldType()
    {
        return ImageType::class;
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
}
