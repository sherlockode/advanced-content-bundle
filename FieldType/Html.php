<?php

namespace Sherlockode\AdvancedContentBundle\FieldType;

use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class Html extends AbstractFieldType
{
    /**
     * @return string
     */
    public function getFormFieldType()
    {
        return TextareaType::class;
    }

    /**
     * @return string
     */
    protected function getDefaultIconClass()
    {
        return 'fa-solid fa-code';
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return 'html';
    }
}
