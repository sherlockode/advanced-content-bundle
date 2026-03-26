<?php

declare(strict_types=1);

namespace Sherlockode\AdvancedContentBundle\FieldType;

use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class TextArea extends AbstractInputType
{
    /**
     * Get options to apply on element.
     *
     * @return array
     */
    #[\Override]
    public function getFormElementOptions()
    {
        $fieldOptions = [];

        $formFieldOptions = parent::getFormElementOptions();
        if (isset($fieldOptions['nbRows'])) {
            $formFieldOptions['attr'] = ['rows' => $fieldOptions['nbRows']];
        }

        return $formFieldOptions;
    }

    public function getFormFieldType(): string
    {
        return TextareaType::class;
    }

    /**
     * Get field's code.
     */
    public function getCode(): string
    {
        return 'textarea';
    }

    public function getPreviewPicture(): ?string
    {
        return 'bundles/sherlockodeadvancedcontent/preview_picture/textarea.svg';
    }
}
