<?php

namespace Sherlockode\AdvancedContentBundle\Form\Type;

use Sherlockode\AdvancedContentBundle\Locale\LocaleProviderInterface;
use Sherlockode\AdvancedContentBundle\Manager\ConfigurationManager;
use Sherlockode\AdvancedContentBundle\Manager\PageManager;
use Sherlockode\AdvancedContentBundle\Model\ContentTypeInterface;
use Sherlockode\AdvancedContentBundle\Model\PageInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PageType extends AbstractType
{
    /**
     * @var ConfigurationManager
     */
    private $configurationManager;

    /**
     * @var PageManager
     */
    private $pageManager;

    /**
     * @var LocaleProviderInterface
     */
    private $localeProvider;

    /**
     * @param ConfigurationManager    $configurationManager
     * @param PageManager             $pageManager
     * @param LocaleProviderInterface $localeProvider
     */
    public function __construct(ConfigurationManager $configurationManager, PageManager $pageManager, LocaleProviderInterface $localeProvider)
    {
        $this->configurationManager = $configurationManager;
        $this->localeProvider = $localeProvider;
        $this->pageManager = $pageManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'page.form.title',
                'attr' => ['class' => 'acb-page-title']
            ])
            ->add('pageType', EntityType::class, [
                'label' => 'page.form.page_type',
                'class' => $this->configurationManager->getEntityClass('page_type'),
                'choice_label' => 'name',
                'choice_translation_domain' => false,
                'required' => false,
                'attr' => ['class' => 'acb-page-page-type'],
            ])
        ;

        $builder->addEventListener(FormEvents::POST_SET_DATA, function(FormEvent $event) {
            $form = $event->getForm();
            /** @var PageInterface $page */
            $page = $event->getData();

            $slugClass = 'acb-page-slug';
            if ($page instanceof PageInterface && $page->getId()) {
                $slugClass = '';
            }
            $form
                ->add('slug', TextType::class, [
                    'label' => 'page.form.slug',
                    'attr' => ['class' => $slugClass],
                ])
            ;

            if ($page instanceof PageInterface && $page->getId()) {
                $form
                    ->add('metaDescription', TextareaType::class, [
                        'label' => 'page.form.meta_description',
                        'required' => false,
                    ])
                    ->add('status', ChoiceType::class, [
                        'label' => 'page.form.status',
                        'choices' => [
                            'page.form.statuses.draft' => PageInterface::STATUS_DRAFT,
                            'page.form.statuses.published' => PageInterface::STATUS_PUBLISHED,
                            'page.form.statuses.trash' => PageInterface::STATUS_TRASH,
                        ],
                        'translation_domain' => 'AdvancedContentBundle',
                    ])
                ;

                $contentType = $this->pageManager->getPageContentType($page);
                if ($contentType instanceof ContentTypeInterface) {
                    if ($this->localeProvider->isMultilangEnabled()) {
                        $form
                            ->add('contents', ContentTranslationType::class, [
                                'label' => 'page.form.content',
                                'contentType' => $contentType,
                            ]);
                    } else {
                        $form
                            ->add('content', ContentType::class, [
                                'label'       => 'page.form.content',
                                'contentType' => $contentType,
                            ])
                        ;
                    }

                }
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => $this->configurationManager->getEntityClass('page'),
            'translation_domain' => 'AdvancedContentBundle',
        ]);
    }
}
