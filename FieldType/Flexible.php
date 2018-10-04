<?php

namespace Sherlockode\AdvancedContentBundle\FieldType;

use Sherlockode\AdvancedContentBundle\Form\DataTransformer\StringToArrayTransformer;
use Sherlockode\AdvancedContentBundle\Form\Type\FlexibleGroupCollectionType;
use Sherlockode\AdvancedContentBundle\Form\Type\FlexibleType;
use Sherlockode\AdvancedContentBundle\Manager\FieldManager;
use Sherlockode\AdvancedContentBundle\Model\FieldInterface;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormBuilderInterface;

class Flexible extends AbstractFieldType
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
            ->add('children', FlexibleGroupCollectionType::class, [
                'contentType' => $field->getContentType(),
                'layouts' => $field->getChildren(),
            ])
        ;
    }

    public function getValueModelTransformer(FieldInterface $field)
    {
        return new StringToArrayTransformer();
    }

    public function addFieldOptions($builder)
    {
        $builder->add('children', FlexibleType::class);
    }

    /**
     * Get field's code
     *
     * @return string
     */
    public function getCode()
    {
        return 'flexible';
    }
}
