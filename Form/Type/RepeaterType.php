<?php

namespace Sherlockode\AdvancedContentBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class RepeaterType extends AbstractType
{
    public function getParent()
    {
        return CollectionType::class;
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'acb_repeater';
    }
}
