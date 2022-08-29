<?php

namespace Sherlockode\AdvancedContentBundle\Import;

use Doctrine\ORM\EntityManagerInterface;
use Sherlockode\AdvancedContentBundle\FieldType\AbstractChoice;
use Sherlockode\AdvancedContentBundle\FieldType\AbstractEntity;
use Sherlockode\AdvancedContentBundle\FieldType\File as FileFieldType;
use Sherlockode\AdvancedContentBundle\Manager\ConfigurationManager;
use Sherlockode\AdvancedContentBundle\Manager\FieldManager;
use Sherlockode\AdvancedContentBundle\Manager\UploadManager;
use Sherlockode\AdvancedContentBundle\Model\ContentInterface;
use Sherlockode\AdvancedContentBundle\Model\FieldGroupValueInterface;
use Sherlockode\AdvancedContentBundle\Model\FieldValueInterface;
use Sherlockode\AdvancedContentBundle\Model\LayoutInterface;
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
     * @var string
     */
    private $rootDir;

    /**
     * @param EntityManagerInterface $em
     * @param ConfigurationManager   $configurationManager
     * @param TranslatorInterface    $translator
     * @param FieldManager           $fieldManager
     * @param UploadManager          $uploadManager
     * @param string                 $rootDir
     */
    public function __construct(
        EntityManagerInterface $em,
        ConfigurationManager $configurationManager,
        TranslatorInterface $translator,
        FieldManager $fieldManager,
        UploadManager $uploadManager,
        $rootDir
    ) {
        parent::__construct($em, $configurationManager, $translator, $fieldManager);

        $this->uploadManager = $uploadManager;
        $this->rootDir = $rootDir;
    }

    /**
     * @param string $slug
     * @param array  $contentData
     */
    protected function importEntity($slug, $contentData)
    {
        if (!isset($contentData['name'])) {
            $this->errors[] = $this->translator->trans('init.errors.content_missing_name', [], 'AdvancedContentBundle');

            return;
        }

        $content = $this->em->getRepository($this->entityClasses['content'])->findOneBy([
            'slug' => $slug,
        ]);

        if (!$content instanceof ContentInterface) {
            $content = new $this->entityClasses['content'];
        } elseif (!$this->allowUpdate) {
            // Content already exist but update is not allowed by configuration
            return;
        }

        $content->setSlug($slug);
        $content->setName($contentData['name']);
        $content->setLocale($contentData['locale'] ?? null);
        if (isset($contentData['children'])) {
            $this->createFieldValues($contentData['children'], $content);
        }

        $this->em->persist($content);
        $this->em->flush();
    }

    /**
     * @param array                         $fieldValuesData
     * @param ContentInterface              $content
     */
    public function createFieldValues(array $fieldValuesData, ContentInterface $content)
    {
        foreach ($fieldValuesData as $fieldValueData) {
            if (!isset($fieldValueData['type'])) {
                $this->errors[] = $this->translator->trans('init.errors.field_value_missing_type', ['%contentName%' => $content->getName()], 'AdvancedContentBundle');
                continue;
            }

            $fieldType = $this->fieldManager->getFieldTypeByCode($fieldValueData['type']);

            /** @var FieldValueInterface $fieldValue */
            $fieldValue = new $this->entityClasses['field_value'];
            $fieldValue->setContent($content);
            $fieldValue->setFieldType($fieldType->getCode());

            $fieldValueValue = '';
            if ($fieldType->getValueModelTransformer() !== null) {
                $fieldValueValue = serialize([]);
            }
            if (isset($fieldValueData['value'])) {
                $fieldValueValue = $fieldValueData['value'];
                if ($fieldType instanceof AbstractEntity) {
                    $hasError = false;
                    if (is_array($fieldValueValue)) {
                        foreach ($fieldValueValue as $value) {
                            if ($fieldType->getEntityByIdentifier($value) === null) {
                                $this->errors[] = $this->translator->trans('init.errors.field_value_entity_not_found', ['%value%' => $value], 'AdvancedContentBundle');
                                $hasError = true;
                            }
                        }
                    } elseif ($fieldType->getEntityByIdentifier($fieldValueValue) === null) {
                        $this->errors[] = $this->translator->trans('init.errors.field_value_entity_not_found', ['%value%' => $fieldValueValue], 'AdvancedContentBundle');
                        $hasError = true;
                    }
                    if ($hasError) {
                        continue;
                    }
                }

                if (is_array($fieldValueValue)) {
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
                                $this->errors[] = $this->translator->trans('init.errors.field_value_invalid_option', ['%option%' => $value, '%options%' => $allowedOptionsAsString], 'AdvancedContentBundle');
                                $invalidOptionFound = true;
                            }
                        }
                        if ($invalidOptionFound) {
                            continue;
                        }
                        $fieldValueValue = $selectedOptionIndexes;
                    } elseif ($fieldType instanceof FileFieldType) {
                        if (isset($fieldValueValue['file'])) {
                            try {
                                $fileName = $this->getFilesDirectory() . $fieldValueValue['file'];
                                if (!file_exists($fileName)) {
                                    $this->errors[] = $this->translator->trans('init.errors.field_value_file_not_found', ['%file%' => $fileName], 'AdvancedContentBundle');
                                    continue;
                                }
                                $file = new File($fileName);
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

            $this->em->persist($fieldValue);

            // look for children groups
            if (isset($fieldValueData['children'])) {
                $childFieldGroupPosition = 0;
                foreach ($fieldValueData['children'] as $fieldChildValues) {
                    $childFieldGroupPosition++;
                    if (!isset($fieldChildValues['name']) && !isset($fieldChildValues['layout_name'])) {
                        $this->errors[] = $this->translator->trans('init.errors.field_group_value_missing_name', [], 'AdvancedContentBundle');
                        continue;
                    }
                    // compatibility with old key "name"
                    $fieldChildName = $fieldChildValues['layout_name'] ?? $fieldChildValues['name'];

                    $childLayout = $this->em->getRepository($this->entityClasses['layout'])->findOneBy([
                        'parent' => $field,
                        'name' => $fieldChildName,
                    ]);
                    if (!$childLayout instanceof LayoutInterface) {
                        $this->errors[] = $this->translator->trans('init.errors.entity_not_found', ['%entity%' => 'Layout', '%name%' => $fieldChildName], 'AdvancedContentBundle');
                        continue;
                    }

                    $childFieldGroupValue = $this->em->getRepository($this->entityClasses['field_group_value'])->findOneBy([
                        'parent' => $fieldValue,
                        'layout' => $childLayout,
                        'position' => $childFieldGroupPosition,
                    ]);
                    if (!$childFieldGroupValue instanceof FieldGroupValueInterface) {
                        /** @var FieldGroupValueInterface $childFieldGroupValue */
                        $childFieldGroupValue = new $this->entityClasses['field_group_value'];
                        $childFieldGroupValue->setParent($fieldValue);
                        $childFieldGroupValue->setLayout($childLayout);
                        $this->em->persist($childFieldGroupValue);
                    }
                    $childFieldGroupValue->setPosition($childFieldGroupPosition);

                    if (isset($fieldChildValues['children'])) {
                        $this->createFieldValues($fieldChildValues['children'], $content);
                    }
                }
            }
        }
    }

    /**
     * @return string
     *
     * @throws \Exception
     */
    private function getFilesDirectory()
    {
        if ($this->filesDirectory === null) {
            $filesDirectory = $this->configurationManager->getInitFilesDirectory();
            if (strpos($filesDirectory, '/') !== 0) {
                $filesDirectory = $this->rootDir . '/' . $filesDirectory;
            }
            if (!file_exists($filesDirectory)) {
                throw new \Exception(
                    $this->translator->trans('init.errors.init_dir', ['%dir%' => $filesDirectory], 'AdvancedContentBundle')
                );
            }
            $this->filesDirectory = $filesDirectory . '/';
        }

        return $this->filesDirectory;
    }

    /**
     * @param string $dir
     */
    public function setFilesDirectory($dir)
    {
        $this->filesDirectory = $dir;
    }
}
