<?php

declare(strict_types=1);

namespace Sherlockode\AdvancedContentBundle\Form\Type;

use Symfony\Component\Form\AbstractType;

class ContentDataType extends AbstractType
{
    #[\Override]
    public function getParent()
    {
        return ElementsType::class;
    }

    #[\Override]
    public function getBlockPrefix()
    {
        return 'acb_content_data';
    }
}
