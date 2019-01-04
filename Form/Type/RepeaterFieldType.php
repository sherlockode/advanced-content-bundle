<?php

namespace Sherlockode\AdvancedContentBundle\Form\Type;

use Sherlockode\AdvancedContentBundle\Form\DataTransformer\FieldValuesTransformer;
use Sherlockode\AdvancedContentBundle\Manager\ConfigurationManager;
use Sherlockode\AdvancedContentBundle\Manager\ContentManager;
use Sherlockode\AdvancedContentBundle\Manager\FieldManager;
use Sherlockode\AdvancedContentBundle\Model\FieldInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class RepeaterFieldType
 *
 * Represents the FieldValue form for 1 child element of a repeater field
 */
class RepeaterFieldType extends AbstractType
{
    private $fieldManager;

    private $configurationManager;

    private $contentManager;

    public function __construct(FieldManager $fieldManager, ConfigurationManager $configurationManager, ContentManager $contentManager)
    {
        $this->fieldManager = $fieldManager;
        $this->configurationManager = $configurationManager;
        $this->contentManager = $contentManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var FieldInterface $field */
        foreach ($options['fields'] as $field) {
            $builder->add($field->getId(), FieldValueType::class, [
                'label'      => $field->getName(),
                'required'   => $field->isRequired(),
                'field_type' => $this->fieldManager->getFieldType($field),
                'field'      => $field,
                'data_class' => $this->configurationManager->getEntityClass('field_value'),
                'translation_domain' => false,
            ]);
        }

        $builder->addViewTransformer(new FieldValuesTransformer($this->contentManager, $options['contentType']));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired(['fields', 'contentType']);
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'acb_repeater_field';
    }
}