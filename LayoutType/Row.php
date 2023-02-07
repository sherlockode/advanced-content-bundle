<?php

namespace Sherlockode\AdvancedContentBundle\LayoutType;

class Row extends AbstractLayoutType
{
    public function getCode()
    {
        return 'row';
    }

    protected function getDefaultIconClass()
    {
        return 'fa-solid fa-bars';
    }
}
