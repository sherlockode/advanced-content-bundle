<?php

declare(strict_types=1);

namespace Sherlockode\AdvancedContentBundle\FieldType;

use Sherlockode\AdvancedContentBundle\Form\Type\PictureType;

class Image extends File
{
    #[\Override]
    public function getFormFieldType(): string
    {
        return PictureType::class;
    }

    #[\Override]
    protected function getDefaultIconClass(): string
    {
        return 'fa-solid fa-image';
    }

    #[\Override]
    public function getPreviewPicture(): ?string
    {
        return 'bundles/sherlockodeadvancedcontent/preview_picture/image.svg';
    }

    /**
     * Get field's code.
     */
    #[\Override]
    public function getCode(): string
    {
        return 'image';
    }

    #[\Override]
    public function getRawValue($element): array
    {
        $element['image'] = parent::getRawValue($element['image'] ?? []);
        $element = array_merge($element, $element['image']);
        if (isset($element['sources']) && is_array($element['sources'])) {
            foreach ($element['sources'] as $key => $source) {
                $element['sources'][$key] = parent::getRawValue($source);
            }
        }

        return $element;
    }
}
