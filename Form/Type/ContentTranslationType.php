<?php

namespace Sherlockode\AdvancedContentBundle\Form\Type;

use Sherlockode\AdvancedContentBundle\Form\DataTransformer\LocaleContentsTransformer;
use Sherlockode\AdvancedContentBundle\Locale\LocaleProviderInterface;
use Sherlockode\AdvancedContentBundle\Model\ContentInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContentTranslationType extends AbstractType
{
    /**
     * @var LocaleProviderInterface
     */
    private $localeProvider;

    /**
     * @param LocaleProviderInterface $localeProvider
     */
    public function __construct(LocaleProviderInterface $localeProvider)
    {
        $this->localeProvider = $localeProvider;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addViewTransformer(new LocaleContentsTransformer());

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $form = $event->getForm();
            $data = $event->getData();
            if (!$data) {
                return;
            }
            $options = $form->getConfig()->getOptions();

            $formLocales = [];
            /** @var ContentInterface $content */
            foreach ($data as $content) {
                $formLocales[$content->getLocale()] = true;
            }
            if (count($formLocales) === 0 && count($options['locales']) > 0) {
                $formLocales[current($options['locales'])] = true;
            }
            foreach ($options['locales'] as $locale) {
                if (isset($formLocales[$locale])) {
                    $form->add($locale, ContentType::class);
                }
            }
        });

        $builder->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) {
            $data = $event->getData();

            /** @var ContentInterface $content */
            foreach ($data as $locale => $content) {
                $content->setLocale($locale);
            }
        });
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $existingLocales = array_keys($form->all());
        $missingLocales = array_diff($this->localeProvider->getLocales(), $existingLocales);

        $view->vars['missingLocales'] = $missingLocales;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'label'       => 'page.form.content',
            'form_type' => ContentType::class,
            'locales' => $this->localeProvider->getLocales(),
            'default_locale' => $this->localeProvider->getDefaultLocale(),
        ]);
    }

    public function getBlockPrefix()
    {
        return 'acb_content_translations';
    }
}
