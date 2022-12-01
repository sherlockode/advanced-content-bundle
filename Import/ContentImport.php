<?php

namespace Sherlockode\AdvancedContentBundle\Import;

use Doctrine\ORM\EntityManagerInterface;
use Sherlockode\AdvancedContentBundle\Manager\ConfigurationManager;
use Sherlockode\AdvancedContentBundle\Manager\FieldManager;
use Sherlockode\AdvancedContentBundle\Manager\UploadManager;
use Sherlockode\AdvancedContentBundle\Model\ContentInterface;
use Sherlockode\AdvancedContentBundle\Model\FieldValueInterface;
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
                $fieldValueValue = [];
            }
            if (isset($fieldValueData['value'])) {
                $fieldValueValue = $fieldValueData['value'];
                if (is_array($fieldValueValue)) {
                    $fieldValueValue = $this->processValueArray($fieldValueValue);
                }
            }
            $fieldValue->setValue($fieldValueValue);

            $this->em->persist($fieldValue);
        }
    }

    private function processValueArray(array $data)
    {
        if (isset($data['_file'])) {
            // handle file
            $result = $this->processFileUpload($data);
            if ($result !== false) {
                $data = $result;
            }
        }
        if (isset($data['_content'])) {
            $slug = $data['_content']['slug'] ?? null;
            $locale = $data['_content']['locale'] ?? null;
            $content = $this->em->getRepository($this->configurationManager->getEntityClass('content'))->findOneBy([
                'slug' => $slug,
                'locale' => $locale,
            ]);
            $data = [
                'content' => $content === null ? null : $content->getId(),
            ];
            if ($content === null) {
                $this->errors[] = $this->translator->trans('init.errors.content_entity_not_found', ['%slug%' => $slug, '%locale%' => $locale], 'AdvancedContentBundle');
            }
        }

        // browse array
        $newData = [];
        foreach ($data as $key => $valueEntry) {
            if (is_array($valueEntry)) {
                $newData[$key] = $this->processValueArray($valueEntry);
            } else {
                $newData[$key] = $valueEntry;
            }
        }

        return $newData;
    }

    private function processFileUpload(array $data)
    {
        try {
            $fileName = $this->getFilesDirectory() . $data['_file'];
            if (!file_exists($fileName)) {
                $this->errors[] = $this->translator->trans('init.errors.field_value_file_not_found', ['%file%' => $fileName], 'AdvancedContentBundle');
                return false;
            }
            $file = new File($fileName);
            $fileName = $this->uploadManager->copy($file);
            $data['src'] = $fileName;
            unset($data['_file']);
        } catch (\Exception $e) {
            $this->errors[] = $e->getMessage();
            return false;
        }

        return $data;
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
