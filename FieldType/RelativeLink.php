<?php

namespace Sherlockode\AdvancedContentBundle\FieldType;

use Sherlockode\AdvancedContentBundle\Manager\UrlBuilderManager;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class RelativeLink extends Link
{
    /**
     * @var UrlBuilderManager
     */
    private $urlBuilderManager;

    /**
     * @param UrlBuilderManager $urlBuilderManager
     */
    public function __construct(UrlBuilderManager $urlBuilderManager)
    {
        $this->urlBuilderManager = $urlBuilderManager;
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

    /**
     * @return string
     */
    protected function getUrlFormType()
    {
        return TextType::class;
    }

    /**
     * @param array $value
     *
     * @return string
     */
    protected function getUrlValue($value)
    {
        return $this->urlBuilderManager->getFullUrl($value['url'] ?? '');
    }
}
