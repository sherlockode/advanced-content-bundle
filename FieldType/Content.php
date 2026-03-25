<?php

namespace Sherlockode\AdvancedContentBundle\FieldType;

use Sherlockode\AdvancedContentBundle\Form\Type\AcbContentType;
use Sherlockode\AdvancedContentBundle\Scope\ScopeHandlerInterface;

class Content extends AbstractFieldType
{
    public function __construct(private readonly ScopeHandlerInterface $scopeHandler)
    {
    }

    /**
     * @return string
     */
    public function getFormFieldType()
    {
        return AcbContentType::class;
    }

    /**
     * Get field's code.
     *
     * @return string
     */
    public function getCode()
    {
        return 'content';
    }

    public function getPreviewPicture(): ?string
    {
        return 'bundles/sherlockodeadvancedcontent/preview_picture/content.svg';
    }

    /**
     * @return array
     */
    #[\Override]
    public function getRawValue($element)
    {
        $element['entity'] = null;

        $contentSlug = $element['content'] ?? null;
        if (null === $contentSlug) {
            return $element;
        }

        $element['entity'] = $this->scopeHandler->getEntityForCurrentScope('content', ['slug' => $contentSlug]);

        return $element;
    }
}
