<?php

namespace Sherlockode\AdvancedContentBundle\Form\Type;

use Sherlockode\AdvancedContentBundle\Manager\ConfigurationManager;
use Sherlockode\AdvancedContentBundle\Model\ContentInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ContentType extends AbstractType
{
    /**
     * @var ConfigurationManager
     */
    private $configurationManager;

    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    /**
     * @param ConfigurationManager  $configurationManager
     * @param UrlGeneratorInterface $urlGenerator
     */
    public function __construct(ConfigurationManager $configurationManager, UrlGeneratorInterface $urlGenerator)
    {
        $this->configurationManager = $configurationManager;
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $token = uniqid('content_');

        $builder
            ->add('name', TextType::class, [
                'label' => 'content.form.name',
                'attr' => ['class' => 'acb-content-name', 'data-slug-token' => $token],
            ])
            ->add('slug', TextType::class)
            ->add('locale', TextType::class, [
                'label' => 'content.form.locale',
            ])
            ->add('data', ElementsType::class, [
                'label' => 'content.form.data',
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

        $builder->addEventListener(FormEvents::POST_SET_DATA, function(FormEvent $event) use ($options, $token) {
            $form = $event->getForm();
            /** @var ContentInterface $content */
            $content = $event->getData();
            $slugClass = 'acb-content-slug';
            if ($content !== null && $content->getId()) {
                $slugClass = '';
            }
            $form
                ->add('slug', TextType::class, [
                    'label' => 'content.form.slug',
                    'attr' => ['class' => $slugClass, 'data-slug-token' => $token],
                ])
            ;
            if ($form->getParent()) {
                $form->remove('name');
                $form->remove('slug');
                $form->remove('locale');
            }
        });

        $builder->get('data')->addEventListener(FormEvents::PRE_SUBMIT, function(FormEvent $event) {
            $event->setData(json_decode($event->getData(), true));
        }, 1);
    }

    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        // ensure the form is working on the first added image (multipart would not be set in this case)
        $view->vars['multipart'] = true;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'translation_domain' => 'AdvancedContentBundle',
        ]);
        $resolver->setDefault('data_class', $this->configurationManager->getEntityClass('content'));
    }
}
