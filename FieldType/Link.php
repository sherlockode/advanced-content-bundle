<?php

namespace Sherlockode\AdvancedContentBundle\FieldType;

use Sherlockode\AdvancedContentBundle\Form\Type\LinkType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;

class Link extends AbstractFieldType
{
    /**
     * @return string
     */
    public function getFormFieldType()
    {
        return LinkType::class;
    }

    #[\Override]
    protected function getDefaultIconClass()
    {
        return 'fa-solid fa-link';
    }

    #[\Override]
    public function getFrontTemplate()
    {
        return '@SherlockodeAdvancedContent/Field/front/link.html.twig';
    }

    #[\Override]
    public function getFormElementOptions()
    {
        return [
            'url_form_type' => $this->getUrlFormType(),
        ];
    }

    /**
     * @return string
     */
    protected function getUrlFormType()
    {
        return UrlType::class;
    }

    /**
     * Get field's code.
     *
     * @return string
     */
    public function getCode()
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
     * @param array $value
     *
     * @return string
     */
    protected function getUrlValue($value)
    {
        return $value['url'] ?? '';
    }
}
