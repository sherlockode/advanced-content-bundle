<?php

declare(strict_types=1);

namespace Sherlockode\AdvancedContentBundle\Element;

use Symfony\Component\Form\FormBuilderInterface;

interface ElementInterface
{
    /**
     * @return string
     */
    public function getIconClass();

    /**
     * @return string
     */
    public function getFormFieldLabel();

    /**
     * @return string
     */
    public function getFrontTemplate();

    public function getPreviewTemplate();

    /**
     * Add element's field(s) to content form.
     *
     * @return void
     */
    public function buildContentElement(FormBuilderInterface $builder);

    /**
     * Get field's code.
     *
     * @return string
     */
    public function getCode();

    /**
     * @param array $element
     *
     * @return array
     */
    public function getRawData($element);
}
