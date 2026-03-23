<?php

declare(strict_types=1);

namespace Sherlockode\AdvancedContentBundle\Form\Type;

use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Sherlockode\AdvancedContentBundle\Manager\ConfigurationManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class WysiwygType extends AbstractType
{
    public function __construct(private readonly ConfigurationManager $configurationManager)
    {
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $toolbar = $this->configurationManager->getDefaultWysiwygToolbar();
        $resolver->setDefaults([
            'config' => ['toolbar' => $toolbar],
        ]);
    }

    public function getParent()
    {
        return CKEditorType::class;
    }
}
