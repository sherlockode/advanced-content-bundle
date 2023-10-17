<?php

namespace Sherlockode\AdvancedContentBundle\Form\Type;

use Symfony\Component\Form\AbstractType;

class ContentDataType extends AbstractType
{
    public function getParent()
    {
        return ElementsType::class;
    }

    public function getBlockPrefix()
    {
        return 'acb_content_data';
    }
}
