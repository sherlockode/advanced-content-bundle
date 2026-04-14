<?php

declare(strict_types=1);

namespace Sherlockode\AdvancedContentBundle\FieldType;

use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;

class Iframe extends AbstractFieldType
{
    public function getFormFieldType(): string
    {
        return FormType::class;
    }

    /**
     * Add element's field(s) to content form.
     */
    #[\Override]
    public function buildContentElement(FormBuilderInterface $builder): void
    {
        parent::buildContentElement($builder);

        $builder->get('value')
            ->add('src', UrlType::class, ['label' => 'field_type.iframe.src'])
            ->add('width', IntegerType::class, ['required' => false, 'label' => 'field_type.iframe.width'])
            ->add('height', IntegerType::class, ['required' => false, 'label' => 'field_type.iframe.height'])
        ;
    }

    /**
     * Get field's code.
     */
    public function getCode(): string
    {
        return 'iframe';
    }

    public function getPreviewPicture(): ?string
    {
        return 'bundles/sherlockodeadvancedcontent/preview_picture/iframe.svg';
    }
}
