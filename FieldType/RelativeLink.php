<?php

namespace Sherlockode\AdvancedContentBundle\FieldType;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\RequestStack;

class RelativeLink extends Link
{
    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @param RequestStack $requestStack
     */
    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
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
        $url = $value['url'] ?? '';
        if (!$url) {
            return '';
        }

        if (substr($url, 0, 1) === '#') {
            return $url;
        }

        if (method_exists($this->requestStack, 'getMainRequest')) {
            // SF >= 5.3
            $mainRequest = $this->requestStack->getMainRequest();
        } else {
            // compat SF < 5.3
            $mainRequest = $this->requestStack->getMasterRequest();
        }
        if (!$mainRequest) {
            return $url;
        }

        return $mainRequest->getSchemeAndHttpHost() . '/' . ltrim($url, '/');
    }
}
