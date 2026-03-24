<?php

declare(strict_types=1);

namespace Sherlockode\AdvancedContentBundle\Form\Type;

use Sherlockode\AdvancedContentBundle\Form\DataTransformer\StringToEntity;
use Symfony\Bridge\Doctrine\Form\Type\EntityType as SymfonyEntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class EntityType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addModelTransformer(new StringToEntity($options['em'], $options['class'], 'id'));
    }

    #[\Override]
    public function getParent()
    {
        return SymfonyEntityType::class;
    }

    #[\Override]
    public function getBlockPrefix()
    {
        return 'acb_entity';
    }
}
