<?php

declare(strict_types=1);

namespace Sherlockode\AdvancedContentBundle\LayoutType;

use Sherlockode\AdvancedContentBundle\Form\Type\RowType;

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

    protected function getConfigurationFormType(): ?string
    {
        return RowType::class;
    }
}
