<?php

namespace Sherlockode\AdvancedContentBundle\Import;

use Doctrine\Common\Persistence\ObjectManager;
use Sherlockode\AdvancedContentBundle\FieldType\AbstractChoice;
use Sherlockode\AdvancedContentBundle\FieldType\File as FileFieldType;
use Sherlockode\AdvancedContentBundle\Manager\ConfigurationManager;
use Sherlockode\AdvancedContentBundle\Manager\FieldManager;
use Sherlockode\AdvancedContentBundle\Manager\UploadManager;
use Sherlockode\AdvancedContentBundle\Model\ContentInterface;
use Sherlockode\AdvancedContentBundle\Model\ContentTypeInterface;
use Sherlockode\AdvancedContentBundle\Model\FieldGroupValueInterface;
use Sherlockode\AdvancedContentBundle\Model\FieldInterface;
use Sherlockode\AdvancedContentBundle\Model\FieldValueInterface;
use Sherlockode\AdvancedContentBundle\Model\LayoutInterface;
use Sherlockode\AdvancedContentBundle\Model\PageInterface;
use Sherlockode\AdvancedContentBundle\Model\PageTypeInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Contracts\Translation\TranslatorInterface;

class ContentImport extends AbstractImport
{
    /**
     * @var string
     */
    private $filesDirectory;

    /**
     * @var UploadManager
     */
    private $uploadManager;

    /**
     * @param ObjectManager        $om
     * @param ConfigurationManager $configurationManager
     * @param TranslatorInterface  $translator
     * @param FieldManager         $fieldManager
     * @param UploadManager        $uploadManager
     */
    public function __construct(
        ObjectManager $om,
        ConfigurationManager $configurationManager,
        TranslatorInterface $translator,
        FieldManager $fieldManager,
        UploadManager $uploadManager
    ) {
        parent::__construct($om, $configurationManager, $translator, $fieldManager);

        $this->uploadManager = $uploadManager;
    }

    /**
     * @param array $contentData
     */
    protected function importData($contentData)
    {
        if (!isset($contentData['name'])) {
            $this->errors[] = $this->translator->trans('init.errors.content_missing_name', [], 'AdvancedContentBundle');

            return;
        }

        if (!isset($contentData['contentType'])) {
            $this->errors[] = $this->translator->trans('init.errors.content_type_missing_name', [], 'AdvancedContentBundle');

            return;
        }

        $contentTypes = $this->om->getRepository($this->entityClasses['content_type'])->findBy([
            'name' => $contentData['contentType']
        ]);

        if (count($contentTypes) !== 1) {
            $transKey = count($contentTypes) > 1 ? 'content_type_too_many_matches' : 'content_no_content_type_found';
            $this->errors[] = $this->translator->trans('init.errors.' . $transKey, ['%name%' => $contentData['contentType']], 'AdvancedContentBundle');

            return;
        }

        /** @var ContentTypeInterface $contentType */
        $contentType = $contentTypes[0];
        if ($contentType->getPageType() instanceof PageTypeInterface || $contentType->getPage() instanceof PageInterface) {
            $this->errors[] = $this->translator->trans('init.errors.content_type_already_linked', ['%name%' => $contentData['contentType']], 'AdvancedContentBundle');

            return;
        }

        $contents = $this->om->getRepository($this->entityClasses['content'])->findBy([
            'name' => $contentData['name'],
            'contentType' => $contentType,
        ]);

        if (count($contents) > 1) {
            $this->errors[] = $this->translator->trans('init.errors.content_too_many_matches', ['%name%' => $contentData['name']], 'AdvancedContentBundle');

            return;
        }

        $content = null;
        if (count($contents) > 0) {
            $content = $contents[0];
        }
        if (!$content instanceof ContentInterface) {
            $content = new $this->entityClasses['content'];
        } elseif (!$this->allowUpdate) {
            // Content already exist but update is not allowed by configuration
            return;
        }

        $content->setName($contentData['name']);
        $content->setContentType($contentType);
        if (isset($contentData['children'])) {
            $this->createFieldValues($contentData['children'], $content);
        }

        $this->om->persist($content);
        $this->om->flush();
    }

    /**
     * @param array                         $fieldValuesData
     * @param ContentInterface              $content
     * @param LayoutInterface|null          $layout
     * @param FieldGroupValueInterface|null $fieldGroupValue
     */
    public function createFieldValues($fieldValuesData, ContentInterface $content, LayoutInterface $layout = null, FieldGroupValueInterface $fieldGroupValue = null)
    {
        foreach ($fieldValuesData as $fieldValueData) {
            if (!isset($fieldValueData['slug'])) {
                $this->errors[] = $this->translator->trans('init.errors.field_value_missing_slug', [], 'AdvancedContentBundle');
                continue;
            }

            $slug = $fieldValueData['slug'];

            if ($layout instanceof LayoutInterface) {
                $field = $this->om->getRepository($this->entityClasses['field'])->findOneBy([
                    'slug'   => $slug,
                    'layout' => $layout,
                ]);
            } else {
                $field = $this->om->getRepository($this->entityClasses['field'])->findOneBy([
                    'slug'        => $slug,
                    'contentType' => $content->getContentType(),
                ]);
            }
            if (!$field instanceof FieldInterface) {
                $this->errors[] = $this->translator->trans('init.errors.entity_not_found', ['%entity%' => 'Field', '%name%' => $slug], 'AdvancedContentBundle');
                continue;
            }

            if ($fieldGroupValue instanceof FieldGroupValueInterface) {
                $fieldValue = $this->om->getRepository($this->entityClasses['field_value'])->findOneBy([
                    'group' => $fieldGroupValue,
                    'field' => $field,
                ]);
            } else {
                $fieldValue = $this->om->getRepository($this->entityClasses['field_value'])->findOneBy([
                    'content' => $content,
                    'field'   => $field,
                ]);
            }
            if (!$fieldValue instanceof FieldValueInterface) {
                $fieldValue = new $this->entityClasses['field_value'];
            }
            if ($fieldGroupValue instanceof FieldGroupValueInterface) {
                $fieldValue->setGroup($fieldGroupValue);
            } else {
                $fieldValue->setContent($content);
            }
            $fieldValue->setField($field);

            $fieldValueValue = serialize([]);
            if (isset($fieldValueData['value'])) {
                $fieldValueValue = $fieldValueData['value'];
                if (is_array($fieldValueValue)) {
                    $fieldType = $this->fieldManager->getFieldType($field);
                    $invalidOptionFound = false;
                    if ($fieldType instanceof AbstractChoice) {
                        $allowedOptions = $fieldType->getFieldOptionsArray($field);
                        $allowedOptionsAsString = '';
                        foreach ($allowedOptions as $allowedOption) {
                            if (!empty($allowedOptionsAsString)) {
                                $allowedOptionsAsString .= ', ';
                            }
                            $allowedOptionsAsString .= '"' . $allowedOption . '"';
                        }
                        $selectedOptionIndexes = [];
                        foreach ($fieldValueValue as $value) {
                            if (false !== $key = array_search($value, $allowedOptions)) {
                                $selectedOptionIndexes[] = $key;
                            } else {
                                $this->errors[] = $this->translator->trans('init.errors.field_value_invalid_option', ['%slug%' => $slug, '%option%' => $value, '%options%' => $allowedOptionsAsString], 'AdvancedContentBundle');
                                $invalidOptionFound = true;
                            }
                        }
                        if ($invalidOptionFound) {
                            continue;
                        }
                        $fieldValueValue = $selectedOptionIndexes;
                    } elseif ($fieldType instanceof FileFieldType) {
                        if (isset($fieldValueValue['file'])) {
                            $fileName = $this->filesDirectory . '/' . $fieldValueValue['file'];
                            if (!file_exists($fileName)) {
                                $this->errors[] = $this->translator->trans('init.errors.field_value_file_not_found', ['%slug%' => $slug, '%file%' => $fileName], 'AdvancedContentBundle');
                                continue;
                            }
                            $file = new File($fileName);
                            try {
                                $fileName = $this->uploadManager->copy($file);
                                $fieldValueValue['src'] = $fileName;
                                unset($fieldValueValue['file']);
                            } catch (\Exception $e) {
                                $this->errors[] = $e->getMessage();
                                continue;
                            }
                        }
                    }
                    $fieldValueValue = serialize($fieldValueValue);
                }
            }
            $fieldValue->setValue($fieldValueValue);

            $this->om->persist($fieldValue);

            if (isset($fieldValueData['children'])) {
                $childFieldGroupPosition = 0;
                foreach ($fieldValueData['children'] as $fieldChildValues) {
                    $childFieldGroupPosition++;
                    if (!isset($fieldChildValues['name'])) {
                        $this->errors[] = $this->translator->trans('init.errors.field_group_value_missing_name', [], 'AdvancedContentBundle');
                        continue;
                    }
                    $fieldChildName = $fieldChildValues['name'];

                    $childLayout = $this->om->getRepository($this->entityClasses['layout'])->findOneBy([
                        'parent' => $field,
                        'name' => $fieldChildName,
                    ]);
                    if (!$childLayout instanceof LayoutInterface) {
                        $this->errors[] = $this->translator->trans('init.errors.entity_not_found', ['%entity%' => 'Layout', '%name%' => $fieldChildName], 'AdvancedContentBundle');
                        continue;
                    }

                    $childFieldGroupValue = $this->om->getRepository($this->entityClasses['field_group_value'])->findOneBy([
                        'parent' => $fieldValue,
                        'layout' => $childLayout,
                        'position' => $childFieldGroupPosition,
                    ]);
                    if (!$childFieldGroupValue instanceof FieldGroupValueInterface) {
                        /** @var FieldGroupValueInterface $childFieldGroupValue */
                        $childFieldGroupValue = new $this->entityClasses['field_group_value'];
                        $childFieldGroupValue->setParent($fieldValue);
                        $childFieldGroupValue->setLayout($childLayout);
                        $this->om->persist($childFieldGroupValue);
                    }
                    $childFieldGroupValue->setPosition($childFieldGroupPosition);

                    if (isset($fieldChildValues['children'])) {
                        $this->createFieldValues($fieldChildValues['children'], $content, $childLayout, $childFieldGroupValue);
                    }
                }
            }
        }
    }

    /**
     * @param string $filesDirectory
     *
     * @return $this
     */
    public function setFilesDirectory($filesDirectory)
    {
        $this->filesDirectory = $filesDirectory;

        return $this;
    }
}
