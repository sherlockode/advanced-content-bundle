<?php

declare(strict_types=1);

namespace Sherlockode\AdvancedContentBundle\Form\Type;

use Sherlockode\AdvancedContentBundle\Manager\ConfigurationManager;
use Sherlockode\AdvancedContentBundle\Scope\ScopeHandlerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ScopeChoiceType extends AbstractType
{
    public function __construct(
        private readonly ConfigurationManager $configurationManager,
        private readonly ScopeHandlerInterface $scopeHandler,
    ) {
    }

    public function configureOptions(OptionsResolver $resolver): void
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

    #[\Override]
    public function getParent(): ?string
    {
        return EntityType::class;
    }
}
