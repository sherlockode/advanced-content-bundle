<?php

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
        return 'layout_type.' . $this->getCode() . '.label';
    }

    /**
     * @return string
     */
    public function getFrontTemplate()
    {
        return '@SherlockodeAdvancedContent/Layout/front/' . $this->getCode() . '.html.twig';
    }

    /**
     * @return string
     */
    public function getPreviewTemplate()
    {
        return '@SherlockodeAdvancedContent/Layout/preview/'. $this->getCode() .'.html.twig';
    }

    /**
     * Add element's field(s) to content form
     *
     * @param FormBuilderInterface $builder
     *
     * @return void
     */
    public function buildContentElement(FormBuilderInterface $builder)
    {
        parent::buildContentElement($builder);

        $builder->add('elements', ElementsType::class, [
            'label' => 'content.form.data',
        ]);
    }

    /**
     * @param array $element
     *
     * @return array
     */
    public function getRawData($element)
    {
        $elements = $element['elements'] ?? [];
        usort($elements, function ($a, $b) {
            return ($a['position'] ?? 0) <=> ($b['position'] ?? 0);
        });

        return ['elements' => $elements];
    }
}
