<?php

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

    /**
     * @return mixed
     */
    public function getPreviewTemplate();

    /**
     * Add element's field(s) to content form
     *
     * @param FormBuilderInterface $builder
     *
     * @return void
     */
    public function buildContentElement(FormBuilderInterface $builder);

    /**
     * Get field's code
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
