<?php

namespace Sherlockode\AdvancedContentBundle\Form\Type;

use Sherlockode\AdvancedContentBundle\Manager\ConfigurationManager;
use Sherlockode\AdvancedContentBundle\Model\PageInterface;
use Sherlockode\AdvancedContentBundle\Scope\ScopeHandlerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Contracts\Translation\TranslatorInterface;

class PageType extends AbstractType
{
    /**
     * @var ConfigurationManager
     */
    private $configurationManager;

    /**
     * @var ScopeHandlerInterface
     */
    private $scopeHandler;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @param ConfigurationManager  $configurationManager
     * @param ScopeHandlerInterface $scopeHandler
     * @param TranslatorInterface   $translator
     */
    public function __construct(
        ConfigurationManager $configurationManager,
        ScopeHandlerInterface $scopeHandler,
        TranslatorInterface $translator
    ) {
        $this->configurationManager = $configurationManager;
        $this->scopeHandler = $scopeHandler;
        $this->translator = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('pageIdentifier', TextType::class, [
                'label' => 'page.form.page_identifier',
                'constraints' => [
                    new NotBlank(null, null, null, null, $options['validation_groups']),
                ],
            ])
            ->add('pageType', EntityType::class, [
                'label' => 'page.form.page_type',
                'class' => $this->configurationManager->getEntityClass('page_type'),
                'choice_label' => 'name',
                'choice_translation_domain' => false,
                'required' => false,
            ])
            ->add('pageMeta', PageMetaType::class, [
                'label'       => 'page.form.page_meta',
            ])
            ->add('content', ContentType::class, [
                'label' => 'page.form.content',
            ])
        ;
        if ($this->configurationManager->isScopesEnabled()) {
            $builder->add('scopes', ScopeChoiceType::class, [
                'label' => 'page.form.scopes',
                'attr' => ['class' => 'acb-scopes'],
            ]);
        }

        $builder->addEventListener(FormEvents::POST_SET_DATA, function(FormEvent $event) {
            $form = $event->getForm();
            /** @var PageInterface $page */
            $page = $event->getData();

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
            }
        });

        // fill the content name and slug as they are not part of the form in Page context
        $builder->addEventListener(FormEvents::SUBMIT, function(FormEvent $event) {
            /** @var PageInterface $page */
            $page = $event->getData();
            $content = $page->getContent();
            if ($content === null) {
                $content = new ($this->configurationManager->getEntityClass('content'));
                $page->setContent($content);
            }
            if (!$content->getId()) {
                $content->setName('page-' . $page->getPageIdentifier() . '-' . bin2hex(random_bytes(6)));
                $content->setSlug($page->getPageMeta()->getSlug() . '-' . bin2hex(random_bytes(6)));
            }

            $form = $event->getForm();
            if (!$this->scopeHandler->isPageSlugValid($page)) {
                if ($this->configurationManager->isScopesEnabled()) {
                    $form->get('pageMeta')->get('slug')->addError(new FormError(
                        $this->translator->trans('page.errors.duplicate_slug_scopes', [], 'AdvancedContentBundle')
                    ));
                } else {
                    $form->get('pageMeta')->get('slug')->addError(new FormError(
                        $this->translator->trans('page.errors.duplicate_slug_no_scope', [], 'AdvancedContentBundle')
                    ));
                }
            }
            if (!$this->scopeHandler->isPageIdentifierValid($page)) {
                if ($this->configurationManager->isScopesEnabled()) {
                    $form->get('pageIdentifier')->addError(new FormError(
                        $this->translator->trans('page.errors.duplicate_identifier_scopes', [], 'AdvancedContentBundle')
                    ));
                } else {
                    $form->get('pageIdentifier')->addError(new FormError(
                        $this->translator->trans('page.errors.duplicate_identifier_no_scope', [], 'AdvancedContentBundle')
                    ));
                }
            }
        });

        $builder->addEventListener(FormEvents::POST_SUBMIT, function(FormEvent $event) {
            $form = $event->getForm();
            if ($form->isValid()) {
                // Reset page version to make sure that page is flagged as to be updated
                $event->getData()->setPageVersion(null);
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
