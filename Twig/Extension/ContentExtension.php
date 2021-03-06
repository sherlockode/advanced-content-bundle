<?php

namespace Sherlockode\AdvancedContentBundle\Twig\Extension;

use Sherlockode\AdvancedContentBundle\Manager\FieldManager;
use Sherlockode\AdvancedContentBundle\Model\ContentInterface;
use Sherlockode\AdvancedContentBundle\Model\FieldValueInterface;
use Sherlockode\AdvancedContentBundle\Model\LayoutInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class ContentExtension extends AbstractExtension
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
     * @return TwigFunction[]
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('acb_fields', [$this, 'getAllFieldValues'], ['is_safe' => ['html']]),
            new TwigFunction('acb_field', [$this, 'getContentFieldValue'], ['is_safe' => ['html']]),
        ];
    }

    /**
     * @param ContentInterface $content
     * @param string           $slug
     *
     * @return null|array
     */
    public function getContentFieldValue(ContentInterface $content, $slug)
    {
        foreach ($content->getFieldValues() as $fieldValue) {
            if ($fieldValue->getField()->getSlug() == $slug) {
                return [
                    'fieldValue' => $fieldValue,
                    'raw' => $this->getFieldRawValue($fieldValue),
                ];
            }
        }
        return null;
    }

    /**
     * @param ContentInterface $content
     *
     * @return array
     */
    public function getAllFieldValues(ContentInterface $content)
    {
        return $this->getFormattedFieldValues($content->getFieldValues());
    }

    /**
     * @param array|FieldValueInterface[] $fieldValues
     *
     * @return array
     */
    private function getFormattedFieldValues($fieldValues)
    {
        $fieldValuesData = [];
        foreach ($fieldValues as $fieldValue) {
            $fieldValuesData[$fieldValue->getField()->getSlug()] = [
                'fieldValue' => $fieldValue,
                'raw' => $this->getFieldRawValue($fieldValue),
            ];
            if (count($fieldValue->getChildren()) > 0) {
                $fieldValuesData[$fieldValue->getField()->getSlug()]['children'] = [];
                foreach ($fieldValue->getChildren() as $fieldGroupValue) {
                    $fieldValuesData[$fieldValue->getField()->getSlug()]['children'][] = [
                        'fieldGroupValue' => $fieldGroupValue,
                        'name' => $fieldGroupValue->getLayout() instanceof LayoutInterface ? $fieldGroupValue->getLayout()->getName() : '',
                        'children' => $this->getFormattedFieldValues($fieldGroupValue->getChildren()),
                    ];
                }
            }
        }

        return $fieldValuesData;
    }

    /**
     * Get FieldValue raw value
     *
     * @param FieldValueInterface $fieldValue
     *
     * @return mixed
     */
    private function getFieldRawValue(FieldValueInterface $fieldValue)
    {
        return $this->fieldManager->getFieldType($fieldValue->getField())->getRawValue($fieldValue);
    }
}
