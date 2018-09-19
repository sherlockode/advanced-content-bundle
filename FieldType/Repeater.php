<?php

namespace Sherlockode\AdvancedContentBundle\FieldType;

use Sherlockode\AdvancedContentBundle\Form\DataTransformer\StringToArrayTransformer;
use Sherlockode\AdvancedContentBundle\Form\Type\FieldType;
use Sherlockode\AdvancedContentBundle\Form\Type\RepeaterGroupCollectionType;
use Sherlockode\AdvancedContentBundle\Form\Type\RepeaterGroupType;
use Sherlockode\AdvancedContentBundle\Form\Type\RepeaterType;
use Sherlockode\AdvancedContentBundle\Manager\FieldManager;
use Sherlockode\AdvancedContentBundle\Model\ContentInterface;
use Sherlockode\AdvancedContentBundle\Model\FieldInterface;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class Repeater extends AbstractFieldType
{
    /**
     * @var FieldManager
     */
    private $fieldManager;

    public function __construct(FieldManager $fieldManager)
    {
        $this->fieldManager = $fieldManager;
    }
    /**
     * @return string
     */
    public function getFormFieldType()
    {
        return FormType::class;
    }

    public function buildContentFieldValue(FormBuilderInterface $builder, FieldInterface $field)
    {
        parent::buildContentFieldValue($builder, $field);

        $builder
            ->add('children', RepeaterGroupCollectionType::class, [
                'label' => false,
                'entry_type' => RepeaterGroupType::class,
                'allow_add' => true,
                'by_reference' => false,
                'entry_options' => ['fields' => $field->getChildren(), 'contentType' => $field->getContentType()]
            ])
        ;
    }

    public function getValueModelTransformer(FieldInterface $field)
    {
        return new StringToArrayTransformer();
    }

    /**
     * Get field's code
     *
     * @return string
     */
    public function getCode()
    {
        return 'repeater';
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
        $builder->add('children', RepeaterType::class, [
            'label' => 'field_type.repeater.field_list',
            'entry_type' => FieldType::class,
            'allow_add' => true,
            'by_reference' => false,
            'entry_options' => [
                'field_type' => $this->fieldManager->getFieldTypeByCode('text'),
                'type_choices' => $this->fieldManager->getFieldTypeFormChoices(),
            ],
        ]);
    }
}
