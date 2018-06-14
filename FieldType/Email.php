<?php

namespace Sherlockode\AdvancedContentBundle\FieldType;

use Symfony\Component\Form\Extension\Core\Type\EmailType;

class Email extends AbstractFieldType
{
    /**
     * @return string
     */
    public function getFormFieldType()
    {
        return EmailType::class;
    }

    /**
     * Get field's code
     *
     * @return string
     */
    public function getCode()
    {
        return 'email';
    }
}
