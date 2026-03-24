<?php

declare(strict_types=1);

namespace Sherlockode\AdvancedContentBundle\FieldType;

use Sherlockode\AdvancedContentBundle\Manager\UrlBuilderManager;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class RelativeLink extends Link
{
    public function __construct(private readonly UrlBuilderManager $urlBuilderManager)
    {
    }

    #[\Override]
    public function getPreviewTemplate(): string
    {
        return '@SherlockodeAdvancedContent/Field/preview/link.html.twig';
    }

    /**
     * Get field's code.
     */
    #[\Override]
    public function getCode(): string
    {
        return 'relative_link';
    }

    #[\Override]
    public function getPreviewPicture(): ?string
    {
        return 'bundles/sherlockodeadvancedcontent/preview_picture/relative_link.svg';
    }

    #[\Override]
    protected function getUrlFormType(): string
    {
        return TextType::class;
    }

    /**
     * @param array $value
     */
    #[\Override]
    protected function getUrlValue($value): string
    {
        return $this->urlBuilderManager->getFullUrl($value['url'] ?? '');
    }
}
