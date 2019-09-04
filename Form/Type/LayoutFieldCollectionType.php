<?php

namespace Sherlockode\AdvancedContentBundle\Form\Type;

use Sherlockode\AdvancedContentBundle\Manager\FieldManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LayoutFieldCollectionType extends AbstractType
{
    /**
     * @var FieldManager
     */
    private $fieldManager;

    public function __construct($fieldManager)
    {
        $this->fieldManager = $fieldManager;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'allow_add' => true,
            'allow_remove' => true,
            'allow_delete' => true,
            'by_reference' => false,
            'entry_type' => FieldType::class,
            'entry_options' => [
                'type_choices' => $this->fieldManager->getFieldTypeFormChoices(),
            ]
        ]);
    }

    public function getBlockPrefix()
    {
        return 'acb_layout_field_collection';
    }

    public function getParent()
    {
        return CollectionType::class;
    }
}
