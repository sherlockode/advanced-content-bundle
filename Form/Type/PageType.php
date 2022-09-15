<?php

namespace Sherlockode\AdvancedContentBundle\Form\Type;

use Sherlockode\AdvancedContentBundle\Locale\LocaleProviderInterface;
use Sherlockode\AdvancedContentBundle\Manager\ConfigurationManager;
use Sherlockode\AdvancedContentBundle\Model\PageInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
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
     * @var LocaleProviderInterface
     */
    private $localeProvider;

    /**
     * @param ConfigurationManager    $configurationManager
     * @param LocaleProviderInterface $localeProvider
     */
    public function __construct(ConfigurationManager $configurationManager, LocaleProviderInterface $localeProvider)
    {
        $this->configurationManager = $configurationManager;
        $this->localeProvider = $localeProvider;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('pageIdentifier', TextType::class, [
                'label' => 'page.form.page_identifier',
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

            if ($this->localeProvider->isMultilangEnabled()) {
                $form
                    ->add('pageMetas', PageMetaTranslationType::class, [
                        'label' => 'page.form.page_meta',
                    ]);
            } else {
                $form
                    ->add('pageMeta', PageMetaType::class, [
                        'label'       => 'page.form.page_meta',
                    ])
                ;
            }

            if ($page instanceof PageInterface && $page->getId()) {
                $form
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

                if ($this->localeProvider->isMultilangEnabled()) {
                    $form
                        ->add('contents', ContentTranslationType::class, [
                            'label' => 'page.form.content',
                        ]);
                } else {
                    $form
                        ->add('content', ContentType::class, [
                            'label' => 'page.form.content',
                        ])
                    ;
                }
            }
        });

        // fill the content name and slug as they are not part of the form in Page context
        $builder->addEventListener(FormEvents::SUBMIT, function(FormEvent $event) {
            /** @var PageInterface $page */
            $page = $event->getData();
            foreach ($page->getContents() as $content) {
                if (!$content->getId()) {
                    $content->setName('page-' . $page->getPageIdentifier() . '-' . bin2hex(random_bytes(6)));
                    $content->setSlug($page->getPageMeta($content->getLocale())->getSlug() . '-' . bin2hex(random_bytes(6)));
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

    public function getBlockPrefix()
    {
        return 'acb_page';
    }
}
