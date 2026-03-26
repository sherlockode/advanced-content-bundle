<?php

namespace Sherlockode\AdvancedContentBundle\FieldType;

use Sherlockode\AdvancedContentBundle\Manager\UrlBuilderManager;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class RelativeLink extends Link
{
    public function __construct(private readonly UrlBuilderManager $urlBuilderManager)
    {
    }

    #[\Override]
    public function getPreviewTemplate()
    {
        return '@SherlockodeAdvancedContent/Field/preview/link.html.twig';
    }

    /**
     * Get field's code.
     *
     * @return string
     */
    #[\Override]
    public function getCode()
    {
        return 'relative_link';
    }

    #[\Override]
    public function getPreviewPicture(): ?string
    {
        return 'bundles/sherlockodeadvancedcontent/preview_picture/relative_link.svg';
    }

    /**
     * @return string
     */
    #[\Override]
    protected function getUrlFormType()
    {
        return TextType::class;
    }

    /**
     * @param array $value
     *
     * @return string
     */
    #[\Override]
    protected function getUrlValue($value)
    {
        return $this->urlBuilderManager->getFullUrl($value['url'] ?? '');
    }
}
