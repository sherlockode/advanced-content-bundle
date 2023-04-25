<?php

namespace Sherlockode\AdvancedContentBundle\Export;

use Sherlockode\AdvancedContentBundle\Exception\InvalidElementException;
use Sherlockode\AdvancedContentBundle\FieldType\Content;
use Sherlockode\AdvancedContentBundle\FieldType\FieldTypeInterface;
use Sherlockode\AdvancedContentBundle\FieldType\File;
use Sherlockode\AdvancedContentBundle\LayoutType\Column;
use Sherlockode\AdvancedContentBundle\LayoutType\LayoutTypeInterface;
use Sherlockode\AdvancedContentBundle\Manager\ElementManager;
use Symfony\Contracts\Translation\TranslatorInterface;

class ElementExport
{
    /**
     * @var ElementManager
     */
    private $elementManager;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @param ElementManager      $elementManager
     * @param TranslatorInterface $translator
     */
    public function __construct(ElementManager $elementManager, TranslatorInterface $translator)
    {
        $this->elementManager = $elementManager;
        $this->translator = $translator;
    }

    /**
     * @param array $elementData
     *
     * @return array
     */
    public function getElementExportData(array $elementData): array
    {
        if (!isset($elementData['elementType'])) {
            throw new \Exception($this->translator->trans('init.errors.element_missing_type', [], 'AdvancedContentBundle'));
        }

        $element = $this->elementManager->getElementByCode($elementData['elementType']);
        if ($element instanceof FieldTypeInterface) {
            $data = $this->getFieldTypeExportData($element, $elementData);
        } elseif ($element instanceof LayoutTypeInterface) {
            $data = $this->getLayoutTypeExportData($element, $elementData);
        } else {
            throw new InvalidElementException(sprintf('Element of type "%s" is not handled in export', get_class($element)));
        }

        return array_merge([
            'type' => $elementData['elementType'],
            'extra' => $elementData['extra'] ?? [],
        ], $data);
    }

    /**
     * @param FieldTypeInterface $element
     * @param array              $elementData
     *
     * @return array
     */
    private function getFieldTypeExportData(FieldTypeInterface $element, array $elementData): array
    {
        $raw = $element->getRawValue($elementData['value'] ?? null);

        if ($element instanceof File) {
            if (is_array($raw) && isset($raw['url'])) {
                unset($raw['url']);
            }
        }
        if ($element instanceof Content) {
            if (array_key_exists('entity', $raw)) {
                unset($raw['entity']);
            }
        }

        return ['value' => $raw];
    }

    /**
     * @param LayoutTypeInterface $element
     * @param array               $elementData
     *
     * @return array[]
     */
    private function getLayoutTypeExportData(LayoutTypeInterface $element, array $elementData): array
    {
        $raw = $element->getRawData($elementData);

        $elements = $raw['elements'] ?? [];
        $elementsData = [];
        foreach ($elements as $childElement) {
            $elementsData[] = $this->getElementExportData($childElement);
        }

        return [
            'elements' => $elementsData,
            'config' => $raw['config'] ?? [],
        ];
    }
}
