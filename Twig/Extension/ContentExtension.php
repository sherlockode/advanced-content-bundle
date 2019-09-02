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

    /**
     * @param FieldManager $fieldManager
     */
    public function __construct(FieldManager $fieldManager)
    {
        $this->fieldManager = $fieldManager;
    }

    /**
     * Add specific twig function
     *
     * @return \Twig_SimpleFunction[]
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('acb_field', [$this, 'displayContentField'], ['is_safe' => ['html']]),
            new \Twig_SimpleFunction('acb_field_value', [$this, 'displayFieldValue'], ['is_safe' => ['html']]),
            new \Twig_SimpleFunction('acb_group_field', [$this, 'displayGroupValue'], ['is_safe' => ['html']]),
            new \Twig_SimpleFunction('acb_groups', [$this, 'getFieldGroupValuesForContent'], ['is_safe' => ['html']]),
            new \Twig_SimpleFunction('acb_group_fields', [$this, 'getGroupFieldValues'], ['is_safe' => ['html']]),
            new \Twig_SimpleFunction('acb_group_all_fields', [$this, 'getGroupAllFieldValues']),
            new \Twig_SimpleFunction('acb_field_raw_value', [$this, 'getFieldRawValue'], ['is_safe' => ['html']]),
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

    /**
     * @param FieldGroupValueInterface|null $group
     *
     * @return FieldValueInterface[]
     */
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

    /**
     * @param FieldValueInterface $fieldValue
     *
     * @return string
     */
    public function displayFieldValue(FieldValueInterface $fieldValue)
    {
        return $this->fieldManager->getFieldType($fieldValue->getField())->render($fieldValue);
    }

    /**
     * Get FieldGroup's children (FieldValues), indexed by Field's slug
     *
     * @param FieldGroupValueInterface $group
     *
     * @return array
     */
    public function getGroupAllFieldValues(FieldGroupValueInterface $group)
    {
        $fieldValues = [];
        foreach ($group->getChildren() as $fieldValue) {
            $fieldValues[$fieldValue->getField()->getSlug()] = $fieldValue;
        }

        return $fieldValues;
    }

    /**
     * Get FieldValue raw value
     *
     * @param FieldValueInterface $fieldValue
     *
     * @return mixed
     */
    public function getFieldRawValue(FieldValueInterface $fieldValue)
    {
        return $this->fieldManager->getFieldType($fieldValue->getField())->getRawValue($fieldValue);
    }
}
