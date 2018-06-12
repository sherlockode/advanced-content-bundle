<?php

namespace Sherlockode\AdvancedContentBundle\FieldType;

class Radio extends AbstractChoice
{
    /**
     * @var bool
     */
    protected $isMultipleChoice = false;

    /**
     * @var bool
     */
    protected $isExpanded = true;

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
