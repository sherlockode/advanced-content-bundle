<?php

namespace Sherlockode\AdvancedContentBundle\LayoutType;

use Sherlockode\AdvancedContentBundle\Form\Type\RowType;

class Row extends AbstractLayoutType
{
    public function getCode()
    {
        return 'row';
    }

    #[\Override]
    protected function getDefaultIconClass()
    {
        return 'fa-solid fa-bars';
    }

    protected function getConfigurationFormType(): ?string
    {
        return RowType::class;
    }
}
