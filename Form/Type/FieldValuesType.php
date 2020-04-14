<?php

namespace Sherlockode\AdvancedContentBundle\Form\Type;

use Sherlockode\AdvancedContentBundle\Form\DataTransformer\FieldValuesTransformer;
use Sherlockode\AdvancedContentBundle\Manager\ConfigurationManager;
use Sherlockode\AdvancedContentBundle\Manager\ContentManager;
use Sherlockode\AdvancedContentBundle\Manager\FieldManager;
use Sherlockode\AdvancedContentBundle\Model\FieldInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FieldValuesType extends AbstractType
{
    /**
     * @var FieldManager
     */
    private $fieldManager;

    /**
     * @var ConfigurationManager
     */
    private $configurationManager;

    /**
     * @var ContentManager
     */
    private $contentManager;

    /**
     * @param FieldManager         $fieldManager
     * @param ConfigurationManager $configurationManager
     * @param ContentManager       $contentManager
     */
    public function __construct(
        FieldManager $fieldManager,
        ConfigurationManager $configurationManager,
        ContentManager $contentManager
    ) {
        $this->fieldManager = $fieldManager;
        $this->configurationManager = $configurationManager;
        $this->contentManager = $contentManager;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var FieldInterface $field */
        foreach ($options['fields'] as $field) {
            if ($field->getLayout()) {
                // do not add fields which are not top-level
                continue;
            }
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

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'translation_domain' => 'AdvancedContentBundle',
        ]);
        $resolver->setRequired(['fields', 'contentType']);
    }
}
