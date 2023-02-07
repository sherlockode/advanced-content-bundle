<?php

namespace Sherlockode\AdvancedContentBundle\LayoutType;

class Column extends AbstractLayoutType
{
    public function getCode()
    {
        return 'column';
    }

    protected function getDefaultIconClass()
    {
        return 'fa-solid fa-columns';
    }
}
