<?php

namespace Sherlockode\AdvancedContentBundle\Form\Type;

use Sherlockode\AdvancedContentBundle\Form\DataTransformer\StringToEntity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType as SymfonyEntityType;

class EntityType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer(new StringToEntity($options['em'], $options['class'], 'id'));
    }

    public function getParent()
    {
        return SymfonyEntityType::class;
    }

    public function getBlockPrefix()
    {
        return 'acb_entity';
    }
}
