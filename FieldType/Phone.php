<?php

namespace Sherlockode\AdvancedContentBundle\FieldType;

use Symfony\Component\Form\Extension\Core\Type\TelType;

class Phone extends AbstractInputType
{
    /**
     * @return string
     */
    public function getFormFieldType()
    {
        return TelType::class;
    }

    /**
     * Get field's code
     *
     * @return string
     */
    public function getCode()
    {
        return 'phone';
    }
}
