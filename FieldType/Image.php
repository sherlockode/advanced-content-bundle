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
        return '@SherlockodeAdvancedContent/Field/image.html.twig';
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

    /**
     * @param string $fileName
     * @param string $value
     *
     * @return string
     */
    protected function renderFile($fileName, $value)
    {
        $alt = $value['alt'] ?? '';

        return '<img src="' . $fileName . '" alt="' . $alt . '" />';
    }
}
