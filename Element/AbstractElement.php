<?php

declare(strict_types=1);

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
     *
     * @return void
     */
    public function buildContentElement(FormBuilderInterface $builder): void
    {
        $builder->add('elementType', HiddenType::class);
        $builder->add('position', HiddenType::class);
    }
}
