<?php

namespace Sherlockode\AdvancedContentBundle\FieldType;

use Sherlockode\AdvancedContentBundle\Model\FieldInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormBuilderInterface;

class Boolean extends AbstractChoice
{
    /**
     * @var bool
     */
    protected $isMultipleChoice = false;

    /**
     * @var bool
     */
    protected $isExpanded = false;

    /**
     * Get field's code
     *
     * @return string
     */
    public function getCode()
    {
        return 'boolean';
    }

    /**
     * Get field's options
     *
     * @param FieldInterface $field
     *
     * @return array
     */
    protected function getFieldOptionsArray(FieldInterface $field)
    {
        return [
            false => 'No',
            true => 'Yes',
        ];
    }

    /**
     * Add field's options
     *
     * @param Form|FormBuilderInterface $builder
     *
     * @return void
     */
    public function addFieldOptions($builder)
    {
    }
}
