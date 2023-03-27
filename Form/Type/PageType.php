<?php

namespace Sherlockode\AdvancedContentBundle\Form\Type;

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
     * @param ConfigurationManager $configurationManager
     */
    public function __construct(ConfigurationManager $configurationManager)
    {
        $this->configurationManager = $configurationManager;
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
            ->add('pageMeta', PageMetaType::class, [
                'label'       => 'page.form.page_meta',
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

                $form
                    ->add('content', ContentType::class, [
                        'label' => 'page.form.content',
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
