<?php

namespace Sherlockode\AdvancedContentBundle\Form\Type;

use Symfony\Component\Form\AbstractType;

class FieldsType extends AbstractType
{
    public function getBlockPrefix()
    {
        return 'acb_fields';
    }
}
