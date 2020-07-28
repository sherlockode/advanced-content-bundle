<?php

namespace Sherlockode\AdvancedContentBundle\Form\Type;

use Sherlockode\AdvancedContentBundle\Form\DataTransformer\LocalePageMetasTransformer;
use Sherlockode\AdvancedContentBundle\Locale\LocaleProviderInterface;
use Sherlockode\AdvancedContentBundle\Model\PageMetaInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PageMetaTranslationType extends AbstractType
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

    /**
     * @inheritDoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addViewTransformer(new LocalePageMetasTransformer());

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $form = $event->getForm();
            $data = $event->getData();
            if (!$data) {
                return;
            }
            $options = $form->getConfig()->getOptions();

            $formLocales = [];
            /** @var PageMetaInterface $pageMeta */
            foreach ($data as $pageMeta) {
                $formLocales[$pageMeta->getLocale()] = true;
            }
            if (count($formLocales) === 0 && count($options['locales']) > 0) {
                $formLocales[current($options['locales'])] = true;
            }
            foreach ($options['locales'] as $locale) {
                if (isset($formLocales[$locale])) {
                    $form->add($locale, PageMetaType::class);
                }
            }
        });

        $builder->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) {
            $data = $event->getData();

            /** @var PageMetaInterface $pageMeta */
            foreach ($data as $locale => $pageMeta) {
                $pageMeta->setLocale($locale);
            }
        });
    }

    /**
     * @inheritDoc
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $existingLocales = array_keys($form->all());
        $missingLocales = array_diff($this->localeProvider->getLocales(), $existingLocales);

        $view->vars['missingLocales'] = $missingLocales;
    }

    /**
     * @inheritDoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'label'       => 'page.form.page_meta',
            'form_type' => PageMetaType::class,
            'locales' => $this->localeProvider->getLocales(),
            'default_locale' => $this->localeProvider->getDefaultLocale(),
        ]);
    }

    /**
     * @inheritDoc
     */
    public function getBlockPrefix()
    {
        return 'acb_pagemeta_translations';
    }
}
