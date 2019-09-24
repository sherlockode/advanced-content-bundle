<?php

namespace Sherlockode\AdvancedContentBundle\Form\Type;

use Doctrine\Common\Persistence\ObjectManager;
use Sherlockode\AdvancedContentBundle\Form\DataTransformer\LayoutToIdTransformer;
use Sherlockode\AdvancedContentBundle\Manager\ConfigurationManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RepeaterGroupType extends AbstractType
{
    /**
     * @var ConfigurationManager
     */
    private $configurationManager;

    /**
     * @var ObjectManager
     */
    private $om;

    /**
     * @param ConfigurationManager $configurationManager
     * @param ObjectManager        $om
     */
    public function __construct(ConfigurationManager $configurationManager, ObjectManager $om)
    {
        $this->configurationManager = $configurationManager;
        $this->om = $om;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $layoutRepository = $this->om->getRepository($this->configurationManager->getEntityClass('layout'));
        $builder->add('layout', HiddenType::class);
        $builder->get('layout')->addViewTransformer(new LayoutToIdTransformer($layoutRepository));
        $builder->add('position', HiddenType::class);
        $builder->add('children', RepeaterFieldType::class, [
            'label' => false,
            'fields' => $options['fields'],
            'contentType' => $options['contentType'],
        ]);
        $builder->addEventListener(FormEvents::POST_SET_DATA, function (FormEvent $event) use ($options) {
            $event->getForm()->get('layout')->setData($options['layout']);
        });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault('data_class', $this->configurationManager->getEntityClass('field_group_value'));
        $resolver->setRequired(['fields', 'contentType', 'layout']);
    }


    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'acb_repeater_group';
    }
}
