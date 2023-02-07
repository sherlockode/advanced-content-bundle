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
     * Add element's field(s) to content form
     *
     * @param FormBuilderInterface $builder
     *
     * @return void
     */
    public function buildContentElement(FormBuilderInterface $builder)
    {
        parent::buildContentElement($builder);

        $builder->add('config', ColumnType::class, [
            'label' => false,
        ]);
    }

    /**
     * @param array $element
     *
     * @return array
     */
    public function getRawData($element)
    {
        return array_merge(parent::getRawData($element), [
            'config' => $element['config'] ?? [],
        ]);
    }
}
