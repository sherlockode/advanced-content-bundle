<?php

namespace Sherlockode\AdvancedContentBundle\FieldType;

use Symfony\Component\Form\Extension\Core\Type\TextType;

class Text extends AbstractInputType
{
    /**
     * @return string
     */
    public function getFormFieldType()
    {
        return TextType::class;
    }

    /**
     * Get field's code
     *
     * @return string
     */
    public function getCode()
    {
        return 'text';
    }
}
