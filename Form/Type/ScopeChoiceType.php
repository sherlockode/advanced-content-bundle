<?php

namespace Sherlockode\AdvancedContentBundle\Form\Type;

use Sherlockode\AdvancedContentBundle\Manager\ConfigurationManager;
use Sherlockode\AdvancedContentBundle\Scope\ScopeHandlerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ScopeChoiceType extends AbstractType
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
     * @param ConfigurationManager  $configurationManager
     * @param ScopeHandlerInterface $scopeHandler
     */
    public function __construct(
        ConfigurationManager $configurationManager,
        ScopeHandlerInterface $scopeHandler
    ) {
        $this->configurationManager = $configurationManager;
        $this->scopeHandler = $scopeHandler;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'class' => $this->configurationManager->getEntityClass('scope'),
            'group_by' => $this->scopeHandler->getScopeGroupBy(),
            'choice_label' => 'optionTitle',
            'multiple' => true,
            'expanded' => false,
            'required' => false,
            'by_reference' => false,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getParent(): ?string
    {
        return EntityType::class;
    }
}
