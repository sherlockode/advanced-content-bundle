<?php

namespace Sherlockode\AdvancedContentBundle\LayoutType;

use Sherlockode\AdvancedContentBundle\Form\Type\ColumnType;

class Column extends AbstractLayoutType
{
    public function getCode()
    {
        return 'column';
    }

    #[\Override]
    protected function getDefaultIconClass()
    {
        return 'fa-solid fa-columns';
    }

    protected function getConfigurationFormType(): ?string
    {
        return ColumnType::class;
    }
}
