<?php

declare(strict_types=1);

namespace Sherlockode\AdvancedContentBundle\FieldType;

use Symfony\Component\Form\Extension\Core\Type\FormType;

class Separator extends AbstractFieldType
{
    public function getFormFieldType(): string
    {
        return FormType::class;
    }

    #[\Override]
    protected function getDefaultIconClass(): string
    {
        return 'fa-solid fa-minus';
    }

    /**
     * Get field's code.
     */
    public function getCode(): string
    {
        return 'separator';
    }

    public function getPreviewPicture(): ?string
    {
        return 'bundles/sherlockodeadvancedcontent/preview_picture/separator.svg';
    }
}
