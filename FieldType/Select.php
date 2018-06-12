<?php

namespace Sherlockode\AdvancedContentBundle\FieldType;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormBuilderInterface;

class Select extends AbstractChoice
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
        return 'select';
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
        $builder->get('options')
            ->add('is_multiple', ChoiceType::class, [
                'choices' => [
                    'No' => false,
                    'Yes' => true,
                ]
            ])
        ;

        parent::addFieldOptions($builder);
    }
}
