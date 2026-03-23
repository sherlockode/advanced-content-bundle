<?php

declare(strict_types=1);

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
     * Get field's code
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
