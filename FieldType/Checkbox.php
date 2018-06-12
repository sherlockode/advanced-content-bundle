<?php

namespace Sherlockode\AdvancedContentBundle\FieldType;

class Checkbox extends AbstractChoice
{
    /**
     * @var bool
     */
    protected $isMultipleChoice = true;

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
        return 'checkbox';
    }
}
