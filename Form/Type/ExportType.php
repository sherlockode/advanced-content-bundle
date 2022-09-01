<?php

namespace Sherlockode\AdvancedContentBundle\Form\Type;

use Doctrine\ORM\EntityRepository;
use Sherlockode\AdvancedContentBundle\Manager\ConfigurationManager;
use Sherlockode\AdvancedContentBundle\Model\PageInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ExportType extends AbstractType
{
    /**
     * @var ConfigurationManager
     */
    private $configurationManager;

    /**
     * @param ConfigurationManager $configurationManager
     */
    public function __construct(ConfigurationManager $configurationManager)
    {
        $this->configurationManager = $configurationManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('page', EntityType::class, [
                'label' => 'tools.export.page',
                'class' => $this->configurationManager->getEntityClass('page'),
                'choice_label' => function(PageInterface $page) {
                    return $page->getPageMeta()->getTitle();
                },
                'expanded' => true,
                'multiple' => true,
                'attr' => ['class' => 'acb-export-entity'],
                'required' => false,
            ])
            ->add('pageAll', CheckboxType::class, [
                'label' => 'tools.export.all',
                'attr' => ['class' => 'acb-export-all'],
                'required' => false,
            ])
            ->add('content', EntityType::class, [
                'label' => 'tools.export.content',
                'class' => $this->configurationManager->getEntityClass('content'),
                'choice_label' => 'name',
                'expanded' => true,
                'multiple' => true,
                'attr' => ['class' => 'acb-export-entity'],
                'required' => false,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('c')
                        ->leftJoin('c.page', 'p')
                        ->where('p.id IS NULL');
                }
            ])
            ->add('contentAll', CheckboxType::class, [
                'label' => 'tools.export.all',
                'attr' => ['class' => 'acb-export-all'],
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'translation_domain' => 'AdvancedContentBundle',
        ]);
    }
}
