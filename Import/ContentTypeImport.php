<?php

namespace Sherlockode\AdvancedContentBundle\Import;

use Sherlockode\AdvancedContentBundle\Exception\InvalidFieldTypeException;
use Sherlockode\AdvancedContentBundle\Model\ContentTypeInterface;
use Sherlockode\AdvancedContentBundle\Model\FieldInterface;
use Sherlockode\AdvancedContentBundle\Model\LayoutInterface;
use Sherlockode\AdvancedContentBundle\Model\PageTypeInterface;

class ContentTypeImport extends AbstractImport
{
    private $defaultRequired;

    protected function init()
    {
        parent::init();

        $this->defaultRequired = $this->configurationManager->getFieldDefaultRequired();
    }

    /**
     * @param string $slug
     * @param array  $contentTypeData
     */
    protected function importEntity($slug, $contentTypeData)
    {
        if (!isset($contentTypeData['name'])) {
            $this->errors[] = $this->translator->trans('init.errors.content_type_missing_name', [], 'AdvancedContentBundle');

            return;
        }

        $contentType = $this->em->getRepository($this->entityClasses['content_type'])->findOneBy([
            'slug' => $slug
        ]);

        if (!$contentType instanceof ContentTypeInterface) {
            $contentType = new $this->entityClasses['content_type'];
        } elseif (!$this->allowUpdate) {
            // ContentType already exist but update is not allowed by configuration
            return;
        }
        $contentType->setSlug($slug);
        $contentType->setName($contentTypeData['name']);

        if (isset($contentTypeData['children'])) {
            $this->createFields($contentTypeData['children'], $contentType);
        }

        if (isset($contentTypeData['pageType'])) {
            $pageTypes = $this->em->getRepository($this->entityClasses['page_type'])->findBy([
                'name' => $contentTypeData['pageType']
            ]);
            if (count($pageTypes) > 1) {
                $this->errors[] = $this->translator->trans('init.errors.page_type_too_many_matches', ['%name%' => $contentTypeData['pageType']], 'AdvancedContentBundle');

                return;
            }

            $pageType = null;
            if (count($pageTypes) > 0) {
                $pageType = $pageTypes[0];
            }
            if (!$pageType instanceof PageTypeInterface) {
                /** @var PageTypeInterface $pageType */
                $pageType = new $this->entityClasses['page_type'];
                $pageType->setName($contentTypeData['pageType']);
                $this->em->persist($pageType);
            }
            $contentType->setPageType($pageType);
            $contentType->setPage(null);
        }

        $this->em->persist($contentType);
        $this->em->flush();
    }

    /**
     * @param array                $fields
     * @param ContentTypeInterface $contentType
     * @param LayoutInterface|null $layout
     */
    private function createFields($fields, ContentTypeInterface $contentType, LayoutInterface $layout = null)
    {
        $slugs = [];
        $position = 1;
        foreach ($fields as $fieldData) {
            if (!isset($fieldData['name']) || !isset($fieldData['type'])) {
                $this->errors[] = $this->translator->trans('init.errors.field_missing_data', [], 'AdvancedContentBundle');
                continue;
            }

            $slug = $this->slugify->slugify($fieldData['name']);
            if (isset($fieldData['slug'])) {
                $slug = $fieldData['slug'];
            }
            if (in_array($slug, $slugs)) {
                $this->errors[] = $this->translator->trans('field_type.errors.duplicated_slug_detail', ['%slug%' => $slug], 'AdvancedContentBundle');
                continue;
            }
            $slugs[] = $slug;

            try {
                $fieldType = $this->fieldManager->getFieldTypeByCode($fieldData['type']);
            } catch (InvalidFieldTypeException $e) {
                $this->errors[] = $this->translator->trans('init.errors.field_invalid_type', ['%slug%' => $slug, '%type%' => $fieldData['type']], 'AdvancedContentBundle');
                continue;
            }

            if (isset($fieldData['options'])) {
                if (!is_array($fieldData['options'])) {
                    $this->errors[] = $this->translator->trans('init.errors.field_options_not_array', ['%slug%' => $slug], 'AdvancedContentBundle');
                    continue;
                }

                $allowedOptions = $fieldType->getFieldOptionNames();
                $allowedOptionsAsString = '';
                foreach ($allowedOptions as $allowedOption) {
                    if (!empty($allowedOptionsAsString)) {
                        $allowedOptionsAsString .= ', ';
                    }
                    $allowedOptionsAsString .= '"' . $allowedOption . '"';
                }
                $invalidOptionFound = false;
                foreach ($fieldData['options'] as $optionName => $value) {
                    if (!in_array($optionName, $allowedOptions)) {
                        $this->errors[] = $this->translator->trans('init.errors.field_invalid_option', ['%slug%' => $slug, '%option%' => $optionName, '%options%' => $allowedOptionsAsString], 'AdvancedContentBundle');
                        $invalidOptionFound = true;
                    }
                }
                if ($invalidOptionFound) {
                    continue;
                }
            }

            if ($layout !== null) {
                $field = $this->em->getRepository($this->entityClasses['field'])->findOneBy([
                    'slug'   => $slug,
                    'layout' => $layout,
                ]);
                if (!$field instanceof FieldInterface) {
                    $field = new $this->entityClasses['field'];
                    $layout->addChild($field);
                }
            } else {
                $field = $this->em->getRepository($this->entityClasses['field'])->findOneBy([
                    'slug'        => $slug,
                    'contentType' => $contentType,
                ]);
                if (!$field instanceof FieldInterface) {
                    $field = new $this->entityClasses['field'];
                    $contentType->addField($field);
                }
            }

            $field->setName($fieldData['name']);
            $field->setSlug($slug);
            $field->setRequired(isset($fieldData['required']) ? (bool) $fieldData['required'] : $this->defaultRequired);
            $field->setType($fieldData['type']);
            $field->setPosition($position++);
            if (isset($fieldData['options'])) {
                $field->setOptions($fieldData['options']);
            }

            $this->em->persist($field);

            if (isset($fieldData['children'])) {
                if ($fieldData['type'] === 'flexible' || $fieldData['type'] === 'repeater') {
                    $layoutPosition = 1;
                    foreach ($fieldData['children'] as $child) {
                        if (!isset($child['name']) && !isset($child['layout_name'])) {
                            $this->errors[] = $this->translator->trans('init.errors.layout_missing_name', [], 'AdvancedContentBundle');
                            continue;
                        }
                        $layoutName = $child['layout_name'] ?? $child['name'];
                        $childLayout = $this->createLayout($layoutName, $layoutPosition++, $field);
                        if (isset($child['children'])) {
                            $this->createFields($child['children'], $contentType, $childLayout);
                        }
                        if ($fieldData['type'] === 'repeater') {
                            // Create only one layout for the repeater field
                            break;
                        }
                    }
                }
            }
        }
    }

    /**
     * @param string          $name
     * @param int             $position
     * @param FieldInterface  $field
     *
     * @return LayoutInterface
     */
    private function createLayout($name, $position, FieldInterface $field)
    {
        $layout = $this->em->getRepository($this->entityClasses['layout'])->findOneBy([
            'name' => $name,
            'parent' => $field,
        ]);
        if (!$layout instanceof LayoutInterface) {
            $layout = new $this->entityClasses['layout'];
        }
        $layout->setName($name);
        $layout->setParent($field);
        $layout->setPosition($position);
        $this->em->persist($layout);

        return $layout;
    }
}
