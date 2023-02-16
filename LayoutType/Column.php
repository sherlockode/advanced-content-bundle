<?php

namespace Sherlockode\AdvancedContentBundle\LayoutType;

use Sherlockode\AdvancedContentBundle\Form\Type\ColumnType;
use Symfony\Component\Form\FormBuilderInterface;

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

    /**
     * @return string|null
     */
    protected function getConfigurationFormType(): ?string
    {
        return ColumnType::class;
    }
}
