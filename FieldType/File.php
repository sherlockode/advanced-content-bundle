<?php

declare(strict_types=1);

namespace Sherlockode\AdvancedContentBundle\FieldType;

use Sherlockode\AdvancedContentBundle\Form\Type\AcbFileType;
use Sherlockode\AdvancedContentBundle\Manager\UrlBuilderManager;

class File extends AbstractFieldType
{
    public function __construct(private readonly UrlBuilderManager $urlBuilderManager)
    {
    }

    public function getFormFieldType(): string
    {
        return AcbFileType::class;
    }

    #[\Override]
    protected function getDefaultIconClass(): string
    {
        return 'fa-solid fa-paperclip';
    }

    /**
     * Get field's code.
     */
    public function getCode(): string
    {
        return 'file';
    }

    public function getPreviewPicture(): ?string
    {
        return 'bundles/sherlockodeadvancedcontent/preview_picture/file.svg';
    }

    protected function getFilename(array $value): string
    {
        return $this->urlBuilderManager->getFileUrl($value['src'] ?? '');
    }

    #[\Override]
    public function getRawValue($element)
    {
        $element['url'] = $this->getFilename($element);

        if (isset($element['delete'])) {
            unset($element['delete']);
        }

        return $element;
    }
}
