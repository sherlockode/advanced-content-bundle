<?php

namespace Sherlockode\AdvancedContentBundle\FieldType;

use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class Password extends AbstractInputType
{
    /**
     * @return string
     */
    public function getFormFieldType()
    {
        return PasswordType::class;
    }

    /**
     * Get field's code
     *
     * @return string
     */
    public function getCode()
    {
        return 'password';
    }
}
