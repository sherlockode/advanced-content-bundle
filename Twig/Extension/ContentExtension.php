<?php

namespace Sherlockode\AdvancedContentBundle\Twig\Extension;

use Sherlockode\AdvancedContentBundle\Manager\FieldManager;
use Sherlockode\AdvancedContentBundle\Model\ContentInterface;

class ContentExtension extends \Twig_Extension
{
    /**
     * @var FieldManager
    */
    private $fieldManager;

    public function __construct(FieldManager $fieldManager)
    {
        $this->fieldManager = $fieldManager;
    }

    /**
     * Add specific twig function
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('acb_field', [$this, 'displayField'], ['is_safe' => ['html']]),
        ];
    }

    /**
     * @param ContentInterface $content
     * @param string           $slug
     *
     * @return bool
     */
    public function displayField(ContentInterface $content = null, $slug)
    {
        if (null === $content) {
            return '';
        }
        foreach ($content->getFieldValues() as $fieldValue) {
            if ($fieldValue->getField()->getSlug() == $slug) {
                return $this->fieldManager->getFieldType($fieldValue->getField())->render($fieldValue);
            }
        }
        return '';
    }
}
