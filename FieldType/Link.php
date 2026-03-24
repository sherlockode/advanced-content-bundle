<?php

declare(strict_types=1);

namespace Sherlockode\AdvancedContentBundle\FieldType;

use Sherlockode\AdvancedContentBundle\Form\Type\LinkType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;

class Link extends AbstractFieldType
{
    public function getFormFieldType(): string
    {
        return LinkType::class;
    }

    #[\Override]
    protected function getDefaultIconClass(): string
    {
        return 'fa-solid fa-link';
    }

    #[\Override]
    public function getFrontTemplate(): string
    {
        return '@SherlockodeAdvancedContent/Field/front/link.html.twig';
    }

    #[\Override]
    public function getFormElementOptions(): array
    {
        return [
            'url_form_type' => $this->getUrlFormType(),
        ];
    }

    protected function getUrlFormType(): string
    {
        return UrlType::class;
    }

    /**
     * Get field's code.
     */
    public function getCode(): string
    {
        return 'link';
    }

    public function getPreviewPicture(): ?string
    {
        return 'bundles/sherlockodeadvancedcontent/preview_picture/link.svg';
    }

    #[\Override]
    public function getRawValue($element)
    {
        $url = $this->getUrlValue($element);

        if (!$url) {
            return null;
        }

        $element['url'] = $url;

        return $element;
    }

    /**
     * @return string
     */
    protected function getUrlValue(array $value)
    {
        return $value['url'] ?? '';
    }
}
