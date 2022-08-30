<?php

namespace Sherlockode\AdvancedContentBundle\Form\Type;

use Sherlockode\AdvancedContentBundle\Manager\ConfigurationManager;
use Sherlockode\AdvancedContentBundle\Model\ContentInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContentType extends AbstractType
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
            ->add('fieldValues', FieldValuesType::class, [
                'label' => 'content.form.field_values',
            ]);
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
                $form->remove('locale');
            }
        });
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
