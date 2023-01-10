<?php

namespace Sherlockode\AdvancedContentBundle\Element;

use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;

abstract class AbstractElement implements ElementInterface
{
    public function getIconClass()
    {
        return $this->getDefaultIconClass();
    }

    /**
     * @return string
     */
    protected function getDefaultIconClass()
    {
        return 'fa-solid fa-gear';
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
        $builder->add('elementType', HiddenType::class);
        $builder->add('position', HiddenType::class);
    }
}
