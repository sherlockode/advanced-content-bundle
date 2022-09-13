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

    public function getFrontTemplate()
    {
        return '@SherlockodeAdvancedContent/Field/front/image.html.twig';
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
