<?php

namespace Sherlockode\AdvancedContentBundle\Twig\Extension;

use Sherlockode\AdvancedContentBundle\Manager\FieldManager;
use Sherlockode\AdvancedContentBundle\Model\ContentInterface;
use Sherlockode\AdvancedContentBundle\Model\FieldGroupValueInterface;
use Sherlockode\AdvancedContentBundle\Model\FieldValueInterface;

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
            new \Twig_SimpleFunction('acb_field', [$this, 'displayContentField'], ['is_safe' => ['html']]),
            new \Twig_SimpleFunction('acb_field_value', [$this, 'displayFieldValue'], ['is_safe' => ['html']]),
            new \Twig_SimpleFunction('acb_group_field', [$this, 'displayGroupValue'], ['is_safe' => ['html']]),
            new \Twig_SimpleFunction('acb_groups', [$this, 'getFieldGroupValuesForContent'], ['is_safe' => ['html']]),
            new \Twig_SimpleFunction('acb_group_fields', [$this, 'getGroupFieldValues'], ['is_safe' => ['html']]),
        ];
    }

    /**
     * @param ContentInterface $content
     * @param string           $slug
     *
     * @return string
     */
    public function displayContentField(ContentInterface $content = null, $slug)
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
    public function getFieldGroupValuesForContent(ContentInterface $content = null, $slug)
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

    public function getGroupFieldValues(FieldGroupValueInterface $group = null)
    {
        if (null === $group) {
            return [];
        }

        return $group->getChildren();
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

    public function displayFieldValue(FieldValueInterface $fieldValue)
    {
        return $this->fieldManager->getFieldType($fieldValue->getField())->render($fieldValue);
    }
}
