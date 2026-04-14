<?php

declare(strict_types=1);

namespace Sherlockode\AdvancedContentBundle\FieldType;

use Symfony\Component\Form\Extension\Core\Type\TextType;

class Text extends AbstractInputType
{
    public function getFormFieldType(): string
    {
        return TextType::class;
    }

    /**
     * Get field's code.
     */
    public function getCode(): string
    {
        return 'text';
    }

    public function getPreviewPicture(): ?string
    {
        return 'bundles/sherlockodeadvancedcontent/preview_picture/text.svg';
    }
}
