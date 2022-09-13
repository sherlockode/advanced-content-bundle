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

    public function getFrontTemplate()
    {
        return '@SherlockodeAdvancedContent/Field/front/title.html.twig';
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
