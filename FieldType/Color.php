<?php

namespace Sherlockode\AdvancedContentBundle\FieldType;

use Symfony\Component\Form\Extension\Core\Type\ColorType;

class Color extends AbstractFieldType
{
    /**
     * @return string
     */
    public function getFormFieldType()
    {
        return ColorType::class;
    }

    /**
     * Get field's code
     *
     * @return string
     */
    public function getCode()
    {
        return 'color';
    }
}
