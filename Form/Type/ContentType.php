<?php

namespace Sherlockode\AdvancedContentBundle\Form\Type;

use Sherlockode\AdvancedContentBundle\Manager\ConfigurationManager;
use Sherlockode\AdvancedContentBundle\Manager\ContentTypeManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContentType extends AbstractType
{
    /**
     * @var ContentTypeManager
     */
    private $contentTypeManager;

    /**
     * @var ConfigurationManager
     */
    private $configurationManager;

    /**
     * @param ContentTypeManager   $contentTypeManager
     * @param ConfigurationManager $configurationManager
     */
    public function __construct(ContentTypeManager $contentTypeManager, ConfigurationManager $configurationManager)
    {
        $this->contentTypeManager = $contentTypeManager;
        $this->configurationManager = $configurationManager;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $fields = $this->contentTypeManager->getOrderedFields($options['contentType']);

        $builder
            ->add('name', TextType::class, [
                'label' => 'content.form.name',
                'required' => false,
            ])
            ->add('fieldValues', FieldValuesType::class, [
                'label' => 'content.form.field_values',
                'fields' => $fields,
                'contentType' => $options['contentType'],
            ])
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'translation_domain' => 'AdvancedContentBundle',
        ]);
        $resolver->setDefault('data_class', $this->configurationManager->getEntityClass('content'));
        $resolver->setRequired(['contentType']);
    }
}
