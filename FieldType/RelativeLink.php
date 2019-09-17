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
        return $this->requestStack->getMasterRequest()->getSchemeAndHttpHost() . '/' . $value['url'] ?? '';
    }
}
