<?php

declare(strict_types=1);

namespace Sherlockode\AdvancedContentBundle\Form\Type;

use Sherlockode\AdvancedContentBundle\Manager\ConfigurationManager;
use Sherlockode\AdvancedContentBundle\Model\ContentInterface;
use Sherlockode\AdvancedContentBundle\Scope\ScopeHandlerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Contracts\Translation\TranslatorInterface;

class ContentType extends AbstractType
{
    public function __construct(
        private readonly ConfigurationManager $configurationManager,
        private readonly UrlGeneratorInterface $urlGenerator,
        private readonly ScopeHandlerInterface $scopeHandler,
        private readonly TranslatorInterface $translator,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $token = uniqid('content_');

        $builder
            ->add('name', TextType::class, [
                'label' => 'content.form.name',
                'attr' => ['class' => 'acb-content-name', 'data-slug-token' => $token],
                'constraints' => [
                    new NotBlank(null, null, null, null, $options['validation_groups']),
                ],
            ])
            ->add('slug', TextType::class, [
                'constraints' => [
                    new NotBlank(null, null, null, null, $options['validation_groups']),
                ],
            ])
            ->add('data', ContentDataType::class, [
                'label' => false,
                'row_attr' => [
                    'class' => 'acb-elements-container',
                    'data-edit-url' => $this->urlGenerator->generate('sherlockode_acb_content_field_form'),
                    'data-new-field-url' => $this->urlGenerator->generate('sherlockode_acb_content_add_field'),
                ],
                'attr' => [
                    'class' => 'acb-elements-form-container',
                ],
            ])
        ;
        if ($this->configurationManager->isScopesEnabled()) {
            $builder->add('scopes', ScopeChoiceType::class, [
                'label' => 'content.form.scopes',
                'attr' => ['class' => 'acb-scopes'],
            ]);
        }

        $builder->addEventListener(FormEvents::POST_SET_DATA, function (FormEvent $event) use ($options, $token): void {
            $form = $event->getForm();
            /** @var ContentInterface $content */
            $content = $event->getData();
            $slugClass = 'acb-content-slug';
            if (null !== $content && $content->getId()) {
                $slugClass = '';
            }

            $form
                ->add('slug', TextType::class, [
                    'label' => 'content.form.slug',
                    'attr' => ['class' => $slugClass, 'data-slug-token' => $token],
                    'constraints' => [
                        new NotBlank(null, null, null, null, $options['validation_groups']),
                    ],
                ])
            ;
            if ($form->getParent() instanceof FormInterface) {
                $form->remove('name');
                $form->remove('slug');
                if ($form->has('scopes')) {
                    $form->remove('scopes');
                }
            }

            if (null === $content || empty($content->getData())) {
                $emptyRowCol = [
                    'elementType' => 'row',
                    'position' => 0,
                    'elements' => [
                        [
                            'elementType' => 'column',
                            'position' => 0,
                            'config' => [
                                'size' => 12,
                            ],
                            'elements' => [],
                        ],
                    ],
                ];
                $form->get('data')->setData([$emptyRowCol]);
            }
        });

        $builder->get('data')->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event): void {
            $event->setData(json_decode((string) $event->getData(), true));
        }, 1);

        $builder->addEventListener(FormEvents::SUBMIT, function (FormEvent $event): void {
            $form = $event->getForm();
            if ($form->has('slug')) {
                $content = $event->getData();
                if (!$this->scopeHandler->isContentSlugValid($content)) {
                    if ($this->configurationManager->isScopesEnabled()) {
                        $form->addError(new FormError(
                            $this->translator->trans('content.errors.duplicate_slug_scopes', [], 'AdvancedContentBundle')
                        ));
                    } else {
                        $form->addError(new FormError(
                            $this->translator->trans('content.errors.duplicate_slug_no_scope', [], 'AdvancedContentBundle')
                        ));
                    }
                }
            }
        });
    }

    public function finishView(FormView $view, FormInterface $form, array $options): void
    {
        // ensure the form is working on the first added image (multipart would not be set in this case)
        $view->vars['multipart'] = true;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'translation_domain' => 'AdvancedContentBundle',
        ]);
        $resolver->setDefault('data_class', $this->configurationManager->getEntityClass('content'));
    }
}
