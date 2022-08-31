<?php

namespace Sherlockode\AdvancedContentBundle\Form\Type;

use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Sherlockode\AdvancedContentBundle\Manager\ConfigurationManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class WysiwygType extends AbstractType
{
    /**
     * @var ConfigurationManager
     */
    private $configurationManager;

    /**
     * @param ConfigurationManager $configurationManager
     */
    public function __construct(ConfigurationManager $configurationManager)
    {
        $this->configurationManager = $configurationManager;
    }

    public function configureOptions(OptionsResolver $resolver)
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
