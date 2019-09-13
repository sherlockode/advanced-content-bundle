<?php

namespace Sherlockode\AdvancedContentBundle\Form\Type;

use Sherlockode\AdvancedContentBundle\Manager\ConfigurationManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LayoutType extends AbstractType
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
        $builder
            ->add('name', TextType::class, [
                'label' => 'content_type.form.layout.name',
                'attr' => ['class' => 'acb-layout-name']
            ])
            ->add('position', HiddenType::class)
            ->add('children', LayoutFieldCollectionType::class, [
                'label' => 'content_type.form.layout.children',
                'attr' => ['class' => 'acb-collection']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => $this->configurationManager->getEntityClass('layout'),
            'translation_domain' => 'AdvancedContentBundle',
        ]);
    }
}
