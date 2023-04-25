<?php

namespace Sherlockode\AdvancedContentBundle\FieldType;

use Sherlockode\AdvancedContentBundle\Form\Type\AcbContentType;
use Sherlockode\AdvancedContentBundle\Scope\ScopeHandlerInterface;

class Content extends AbstractFieldType
{
    /**
     * @var ScopeHandlerInterface
     */
    private $scopeHandler;

    /**
     * @param ScopeHandlerInterface $scopeHandler
     */
    public function __construct(
        ScopeHandlerInterface $scopeHandler
    ) {
        $this->scopeHandler = $scopeHandler;
    }

    /**
     * @return string
     */
    public function getFormFieldType()
    {
        return AcbContentType::class;
    }

    /**
     * Get field's code
     *
     * @return string
     */
    public function getCode()
    {
        return 'content';
    }

    /**
     * @param mixed $element
     *
     * @return array
     */
    public function getRawValue($element)
    {
        $element['entity'] = null;

        $contentSlug = $element['content'] ?? null;
        if ($contentSlug === null) {
            return $element;
        }

        $element['entity'] = $this->scopeHandler->getEntityForCurrentScope('content', ['slug' => $contentSlug]);

        return $element;
    }
}
