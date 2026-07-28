<?php

declare(strict_types=1);

namespace Sherlockode\AdvancedContentBundle\LayoutType;

use Sherlockode\AdvancedContentBundle\Element\AbstractElement;
use Sherlockode\AdvancedContentBundle\Form\Type\ElementsType;
use Symfony\Component\Form\FormBuilderInterface;

abstract class AbstractLayoutType extends AbstractElement implements LayoutTypeInterface
{
    /**
     * @return string
     */
    public function getFormFieldLabel()
    {
        return 'layout_type.'.$this->getCode().'.label';
    }

    /**
     * @return string
     */
    public function getFrontTemplate()
    {
        return '@SherlockodeAdvancedContent/Layout/front/'.$this->getCode().'.html.twig';
    }

    /**
     * @return string
     */
    public function getPreviewTemplate()
    {
        return '@SherlockodeAdvancedContent/Layout/preview/'.$this->getCode().'.html.twig';
    }

    /**
     * Add element's field(s) to content form.
     */
    #[\Override]
    public function buildContentElement(FormBuilderInterface $builder): void
    {
        parent::buildContentElement($builder);

        $builder->add('elements', ElementsType::class, [
            'label' => false,
            'row_attr' => [
                'class' => 'acb-layout-elements-container',
            ],
        ]);

        $configurationFormType = $this->getConfigurationFormType();
        if (null !== $configurationFormType) {
            $builder->add('config', $configurationFormType, [
                'label' => false,
            ]);
        }
    }

    /**
     * @param array $element
     *
     * @return array
     */
    public function getRawData($element)
    {
        $elements = $element['elements'] ?? [];
        uasort($elements, fn ($a, $b): int => ($a['position'] ?? 0) <=> ($b['position'] ?? 0));

        return [
            'elements' => $elements,
            'config' => $element['config'] ?? [],
            'extra' => $element['extra'] ?? [],
        ];
    }

    /**
     * Get layout configuration form type.
     */
    abstract protected function getConfigurationFormType(): ?string;
}
