<?php

namespace Sherlockode\AdvancedContentBundle\FieldType;

use Sherlockode\AdvancedContentBundle\Form\Type\TitleType;

class Title extends AbstractFieldType
{
    /**
     * @return string
     */
    public function getFormFieldType()
    {
        return TitleType::class;
    }

    #[\Override]
    protected function getDefaultIconClass()
    {
        return 'fa-solid fa-heading';
    }

    /**
     * Get field's code.
     *
     * @return string
     */
    public function getCode()
    {
        return 'title';
    }

    public function getPreviewPicture(): ?string
    {
        return 'bundles/sherlockodeadvancedcontent/preview_picture/title.svg';
    }
}
