<?php

namespace Sherlockode\AdvancedContentBundle\Form\Type;

use Doctrine\ORM\EntityRepository;
use Sherlockode\AdvancedContentBundle\Locale\LocaleProviderInterface;
use Sherlockode\AdvancedContentBundle\Manager\ConfigurationManager;
use Sherlockode\AdvancedContentBundle\Model\ContentInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class AcbContentType extends AbstractType
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

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('content', EntityType::class, [
                'label' => false,
                'class' => $this->configurationManager->getEntityClass('content'),
                'query_builder' => function (EntityRepository $repository) {
                    return $repository->createQueryBuilder('content')
                        ->where('content.page IS NULL');
                },
                'choice_label' => function (ContentInterface $content) {
                    $label = $content->getName();
                    if ($this->localeProvider->isMultilangEnabled()) {
                        $label .= ' - ' . $content->getLocale();
                    }

                    return $label;
                },
            ])
        ;
    }
}
