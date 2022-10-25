<?php

namespace Sherlockode\AdvancedContentBundle\FieldType;

use Sherlockode\AdvancedContentBundle\Form\Type\TitleType;

class Title extends AbstractFieldType
{
    /**
     * @return string
     */
    public function getFormFieldType()
    {
        return TitleType::class;
    }

    protected function getDefaultIconClass()
    {
        return 'fa-solid fa-heading';
    }

    /**
     * Get field's code
     *
     * @return string
     */
    public function getCode()
    {
        return 'title';
    }
}
