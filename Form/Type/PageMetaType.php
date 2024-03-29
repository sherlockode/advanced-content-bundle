<?php

namespace Sherlockode\AdvancedContentBundle\Form\Type;

use Sherlockode\AdvancedContentBundle\Manager\ConfigurationManager;
use Sherlockode\AdvancedContentBundle\Model\PageMetaInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class PageMetaType extends AbstractType
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

    /**
     * @inheritDoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $token = uniqid('page_meta_');
        $builder
            ->add('title', TextType::class, [
                'label' => 'page.form.title',
                'attr' => [
                    'class' => 'acb-pagemeta-title',
                    'data-slug-token' => $token,
                    'data-page-draft' => 'title',
                ],
                'constraints' => [
                    new NotBlank(null, null, null, null, $options['validation_groups']),
                ],
            ])
            ->add('slug', TextType::class, [
                'attr' => [
                    'data-page-draft' => 'slug',
                ],
                'constraints' => [
                    new NotBlank(null, null, null, null, $options['validation_groups']),
                ],
            ])
            ->add('metaTitle', TextType::class, [
                'label' => 'page.form.meta_title',
                'required' => false,
                'attr' => [
                    'data-page-draft' => 'metaTitle',
                ],
            ])
            ->add('metaDescription', TextType::class, [
                'label' => 'page.form.meta_description',
                'required' => false,
                'attr' => [
                    'data-page-draft' => 'metaDescription',
                ],
            ])
        ;

        $builder->addEventListener(FormEvents::POST_SET_DATA, function(FormEvent $event) use ($options, $token) {
            $form = $event->getForm();
            /** @var PageMetaInterface $pageMeta */
            $pageMeta = $event->getData();
            $slugClass = 'acb-pagemeta-slug';
            if ($pageMeta !== null && $pageMeta->getId()) {
                $slugClass = '';
            }
            $form
                ->add('slug', TextType::class, [
                    'label' => 'page.form.slug',
                    'attr' => [
                        'class' => $slugClass,
                        'data-slug-token' => $token,
                        'data-page-draft' => 'slug',
                    ],
                    'constraints' => [
                        new NotBlank(null, null, null, null, $options['validation_groups']),
                    ],
                ])
            ;
        });
    }

    /**
     * @inheritDoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'translation_domain' => 'AdvancedContentBundle',
            'data_class' => $this->configurationManager->getEntityClass('page_meta'),
            'attr' => ['class' => 'acb-page-meta-container acb-page-field-container'],
        ]);
    }
}
