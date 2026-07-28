<?php

declare(strict_types=1);

namespace Sherlockode\AdvancedContentBundle\FieldType;

use Sherlockode\AdvancedContentBundle\Form\Type\TitleType;

class Title extends AbstractFieldType
{
    public function getFormFieldType(): string
    {
        return TitleType::class;
    }

    #[\Override]
    protected function getDefaultIconClass(): string
    {
        return 'fa-solid fa-heading';
    }

    /**
     * Get field's code.
     */
    public function getCode(): string
    {
        return 'title';
    }

    public function getPreviewPicture(): ?string
    {
        return 'bundles/sherlockodeadvancedcontent/preview_picture/title.svg';
    }
}
