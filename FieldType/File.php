<?php

namespace Sherlockode\AdvancedContentBundle\FieldType;

use Sherlockode\AdvancedContentBundle\Form\Type\AcbFileType;
use Sherlockode\AdvancedContentBundle\Manager\UrlBuilderManager;

class File extends AbstractFieldType
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

    /**
     * @return string
     */
    public function getFormFieldType()
    {
        return AcbFileType::class;
    }

    protected function getDefaultIconClass()
    {
        return 'fa-solid fa-paperclip';
    }

    /**
     * Get field's code
     *
     * @return string
     */
    public function getCode()
    {
        return 'file';
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

    /**
     * @param mixed $element
     *
     * @return mixed
     */
    public function getRawValue($element)
    {
        $element['url'] = $this->getFilename($element);

        if (isset($element['delete'])) {
            unset($element['delete']);
        }

        return $element;
    }
}
