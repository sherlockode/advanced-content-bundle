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

    public function getPreviewTemplate()
    {
        return '@SherlockodeAdvancedContent/Field/preview/link.html.twig';
    }

    /**
     * Get field's code
     *
     * @return string
     */
    public function getCode()
    {
        return 'relative_link';
    }

    public function getPreviewPicture(): ?string
    {
        return 'bundles/sherlockodeadvancedcontent/preview_picture/relative_link.svg';
    }

    protected function getUrlFormType(): string
    {
        return TextType::class;
    }

    protected function getUrlValue(array $value): string
    {
        return $this->urlBuilderManager->getFullUrl($value['url'] ?? '');
    }
}
