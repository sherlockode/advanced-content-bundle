<?php

namespace Sherlockode\AdvancedContentBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Sherlockode\AdvancedContentBundle\Locale\LocaleProviderInterface;
use Sherlockode\AdvancedContentBundle\Model\PageInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class TranslationListener
{
    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @var LocaleProviderInterface
     */
    private $localeProvider;

    /**
     * TranslationListener constructor.
     *
     * @param RequestStack            $requestStack
     * @param LocaleProviderInterface $localeProvider
     */
    public function __construct(RequestStack $requestStack, LocaleProviderInterface $localeProvider)
    {
        $this->requestStack = $requestStack;
        $this->localeProvider = $localeProvider;
    }

    public function postLoad(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if (!$entity instanceof PageInterface) {
            return;
        }
        if ($this->requestStack->getCurrentRequest() === null) {
            return;
        }

        $locale = $this->requestStack->getCurrentRequest()->getLocale();
        if (!in_array($locale, $this->localeProvider->getLocales())) {
            $locale = $this->localeProvider->getDefaultLocale();
        }
        $entity->setCurrentLocale($locale);
    }
}
