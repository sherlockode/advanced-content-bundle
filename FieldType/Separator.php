<?php

namespace Sherlockode\AdvancedContentBundle\FieldType;

use Symfony\Component\Form\Extension\Core\Type\FormType;

class Separator extends AbstractFieldType
{
    /**
     * @return string
     */
    public function getFormFieldType()
    {
        return FormType::class;
    }

    protected function getDefaultIconClass()
    {
        return 'fa-solid fa-minus';
    }

    /**
     * Get field's code
     *
     * @return string
     */
    public function getCode()
    {
        return 'separator';
    }
}
