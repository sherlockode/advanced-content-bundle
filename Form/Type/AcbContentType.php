<?php

namespace Sherlockode\AdvancedContentBundle\Form\Type;

use Doctrine\ORM\EntityManagerInterface;
use Sherlockode\AdvancedContentBundle\Manager\ConfigurationManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;

class AcbContentType extends AbstractType
{
    /**
     * @var ConfigurationManager
     */
    private $configurationManager;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(ConfigurationManager $configurationManager, EntityManagerInterface $em)
    {
        $this->configurationManager = $configurationManager;
        $this->em = $em;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $contents = $this->em->getRepository($this->configurationManager->getEntityClass('content'))->findBy([
            'page' => null,
        ], ['slug' => 'ASC']);
        $slugs = [];
        foreach ($contents as $content) {
            $slugs[$content->getSlug()] = $content->getSlug();
        }

        $builder
            ->add('content', ChoiceType::class, [
                'label' => false,
                'choices' => $slugs,
            ])
        ;
    }
}
