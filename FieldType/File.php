<?php

namespace Sherlockode\AdvancedContentBundle\FieldType;

use Sherlockode\AdvancedContentBundle\Form\Type\AcbFileType;
use Sherlockode\AdvancedContentBundle\Manager\UrlBuilderManager;

class File extends AbstractFieldType
{
    public function __construct(private readonly UrlBuilderManager $urlBuilderManager)
    {
    }

    /**
     * @return string
     */
    public function getFormFieldType()
    {
        return AcbFileType::class;
    }

    #[\Override]
    protected function getDefaultIconClass()
    {
        return 'fa-solid fa-paperclip';
    }

    /**
     * Get field's code.
     *
     * @return string
     */
    public function getCode()
    {
        return 'file';
    }

    public function getPreviewPicture(): ?string
    {
        return 'bundles/sherlockodeadvancedcontent/preview_picture/file.svg';
    }

    /**
     * @param array $value
     *
     * @return string
     */
    protected function getFilename($value)
    {
        return $this->urlBuilderManager->getFileUrl($value['src'] ?? '');
    }

    #[\Override]
    public function getRawValue($element)
    {
        $element['url'] = $this->getFilename($element);

        if (isset($element['delete'])) {
            unset($element['delete']);
        }

        return $element;
    }
}
