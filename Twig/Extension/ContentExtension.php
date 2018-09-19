<?php

namespace Sherlockode\AdvancedContentBundle\Twig\Extension;

use Sherlockode\AdvancedContentBundle\Manager\FieldManager;
use Sherlockode\AdvancedContentBundle\Model\ContentInterface;
use Sherlockode\AdvancedContentBundle\Model\FieldGroupValueInterface;

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
            new \Twig_SimpleFunction('acb_group_field', [$this, 'displayGroupValue'], ['is_safe' => ['html']]),
            new \Twig_SimpleFunction('acb_groups', [$this, 'getFieldGroupValues'], ['is_safe' => ['html']]),
        ];
    }

    /**
     * @param ContentInterface $content
     * @param string           $slug
     *
     * @return string
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

    /**
     * @param ContentInterface|null $content
     * @param string                $slug
     *
     * @return FieldGroupValueInterface[]
     */
    public function getFieldGroupValues(ContentInterface $content = null, $slug)
    {
        if (null === $content) {
            return [];
        }

        foreach ($content->getFieldValues() as $fieldValue) {
            if ($fieldValue->getField()->getSlug() == $slug) {
                return $fieldValue->getChildren();
            }
        }
        return [];
    }

    /**
     * @param FieldGroupValueInterface $group
     * @param string                   $slug
     *
     * @return string
     */
    public function displayGroupValue(FieldGroupValueInterface $group, $slug)
    {
        foreach ($group->getChildren() as $fieldValue) {
            if ($fieldValue->getField()->getSlug() == $slug) {
                return $this->fieldManager->getFieldType($fieldValue->getField())->render($fieldValue);
            }
        }

        return '';
    }
}
