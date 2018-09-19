<?php

namespace Sherlockode\AdvancedContentBundle\Form\Type;

use Sherlockode\AdvancedContentBundle\Manager\ConfigurationManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RepeaterGroupType extends AbstractType
{
    /**
     * @var ConfigurationManager
     */
    private $configurationManager;

    public function __construct(ConfigurationManager $configurationManager)
    {
        $this->configurationManager = $configurationManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('children', RepeaterFieldType::class, [
            'label' => false,
            'fields' => $options['fields'],
            'contentType' => $options['contentType'],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault('data_class', $this->configurationManager->getEntityClass('field_group_value'));
        $resolver->setRequired(['fields', 'contentType']);
    }
}
