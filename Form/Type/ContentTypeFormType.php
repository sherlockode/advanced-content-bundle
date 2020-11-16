<?php

namespace Sherlockode\AdvancedContentBundle\Form\Type;

use Sherlockode\AdvancedContentBundle\Manager\ConfigurationManager;
use Sherlockode\AdvancedContentBundle\Manager\ContentTypeManager;
use Sherlockode\AdvancedContentBundle\Manager\FieldManager;
use Sherlockode\AdvancedContentBundle\Manager\PageManager;
use Sherlockode\AdvancedContentBundle\Model\ContentTypeInterface;
use Sherlockode\AdvancedContentBundle\Model\PageInterface;
use Sherlockode\AdvancedContentBundle\Model\PageTypeInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class ContentTypeFormType extends AbstractType
{
    /**
     * @var ContentTypeManager
     */
    private $contentTypeManager;

    /**
     * @var FieldManager
     */
    private $fieldManager;

    /**
     * @var ConfigurationManager
     */
    private $configurationManager;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var PageManager
     */
    private $pageManager;

    /**
     * @param ContentTypeManager   $contentTypeManager
     * @param FieldManager         $fieldManager
     * @param ConfigurationManager $configurationManager
     * @param TranslatorInterface  $translator
     * @param PageManager          $pageManager
     */
    public function __construct(
        ContentTypeManager $contentTypeManager,
        FieldManager $fieldManager,
        ConfigurationManager $configurationManager,
        TranslatorInterface $translator,
        PageManager $pageManager
    ) {
        $this->contentTypeManager = $contentTypeManager;
        $this->fieldManager = $fieldManager;
        $this->configurationManager = $configurationManager;
        $this->translator = $translator;
        $this->pageManager = $pageManager;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'content_type.form.name',
                'attr' => ['class' => 'acb-contenttype-name'],
            ])
            ->add('linkType', ChoiceType::class, [
                'label' => 'content_type.form.link_types.label',
                'choices' => [
                    'content_type.form.link_types.no_link' => ContentTypeInterface::LINK_TYPE_NO_LINK,
                    'content_type.form.link_types.page_type' => ContentTypeInterface::LINK_TYPE_PAGE_TYPE,
                    'content_type.form.link_types.page' => ContentTypeInterface::LINK_TYPE_PAGE
                ],
                'translation_domain' => 'AdvancedContentBundle',
                'mapped' => false,
                'attr' => ['class' => 'acb-contenttype-link-type'],
            ])
            ->add('pageType', EntityType::class, [
                'label' => 'content_type.form.page_type',
                'class' => $this->configurationManager->getEntityClass('page_type'),
                'choice_label' => 'name',
                'required' => false,
                'attr' => ['class' => 'acb-contenttype-page-type'],
            ])
            ->add('page', EntityType::class, [
                'label' => 'content_type.form.page',
                'class' => $this->configurationManager->getEntityClass('page'),
                'choice_label' => 'pageMeta.title',
                'required' => false,
                'attr' => ['class' => 'acb-contenttype-page'],
            ])
            ->add('allowSeveralContents', CheckboxType::class, [
                'label' => 'content_type.form.allow_several_contents',
                'required' => false,
                'attr' => ['class' => 'acb-contenttype-allow-several-contents'],
            ])
        ;

        $builder->addEventListener(FormEvents::POST_SET_DATA, function(FormEvent $event) use ($options) {
            $form = $event->getForm();
            /** @var ContentTypeInterface $contentType */
            $contentType = $options['contentType'];
            $linkType = ContentTypeInterface::LINK_TYPE_NO_LINK;
            if ($contentType->getPage() instanceof PageInterface) {
                $linkType = ContentTypeInterface::LINK_TYPE_PAGE;
            } elseif ($contentType->getPageType() instanceof PageTypeInterface) {
                $linkType = ContentTypeInterface::LINK_TYPE_PAGE_TYPE;
            }
            $form->get('linkType')->setData($linkType);

            $slugClass = 'acb-contenttype-slug';
            if ($contentType->getId()) {
                $slugClass = '';
            }
            $form
                ->add('slug', TextType::class, [
                    'label' => 'content_type.form.slug',
                    'attr' => ['class' => $slugClass],
                ])
            ;
        });
        $builder->addEventListener(FormEvents::PRE_SUBMIT, function(FormEvent $event) {
            $form = $event->getForm();
            $data = $event->getData() ?? [];

            /** @var ContentTypeInterface $contentType */
            $contentType = $form->getData();
            $linkType = $data['linkType'];
            if ($linkType == ContentTypeInterface::LINK_TYPE_PAGE) {
                $data['pageType'] = null;
                if (empty($data['page'])) {
                    $form->addError(new FormError(
                        $this->translator->trans('content_type.errors.missing_page', [], 'AdvancedContentBundle')
                    ));
                    return;
                }
            }
            if ($linkType == ContentTypeInterface::LINK_TYPE_PAGE_TYPE) {
                $data['page'] = null;
                if (empty($data['pageType'])) {
                    $form->addError(new FormError(
                        $this->translator->trans('content_type.errors.missing_page_type', [], 'AdvancedContentBundle')
                    ));
                    return;
                }
            }

            $allowSeveralContents = true;
            if ($linkType == ContentTypeInterface::LINK_TYPE_NO_LINK) {
                $data['pageType'] = null;
                $data['page'] = null;
                $allowSeveralContents = isset($data['allowSeveralContents']) ? $data['allowSeveralContents'] : false;
            } else {
                if (!$this->pageManager->validateContentTypeLink($contentType, $data['pageType'], $data['page'])) {
                    $form->addError(new FormError(
                        $this->translator->trans('content_type.errors.existing_link', [], 'AdvancedContentBundle')
                    ));

                    return;
                }
            }
            $data['allowSeveralContents'] = $allowSeveralContents;

            $event->setData($data);
        });

        if (!$options['contentType']->getId()) {
            return;
        }

        $fieldTypeChoices = $this->fieldManager->getFieldTypeFormChoices();
        $fieldsBuilder = $builder->create('fields', FieldsType::class, [
            'label' => 'content_type.form.fields',
            'translation_domain' => 'AdvancedContentBundle',
            'type_choices' => $fieldTypeChoices,
        ]);

        $builder->add($fieldsBuilder);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'translation_domain' => 'AdvancedContentBundle',
        ]);
        $resolver->setRequired(['contentType']);
    }
}
