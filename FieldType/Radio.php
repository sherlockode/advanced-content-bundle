<?php

namespace Sherlockode\AdvancedContentBundle\FieldType;

class Radio extends AbstractChoice
{
    /**
     * @var bool
     */
    protected $isMultipleChoice = false;

    /**
     * Get field's code
     *
     * @return string
     */
    public function getCode()
    {
        return 'radio';
    }
}
