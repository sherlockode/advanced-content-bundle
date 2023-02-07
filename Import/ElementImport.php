<?php

namespace Sherlockode\AdvancedContentBundle\Import;

use Doctrine\ORM\EntityManagerInterface;
use Sherlockode\AdvancedContentBundle\Exception\InvalidElementException;
use Sherlockode\AdvancedContentBundle\FieldType\FieldTypeInterface;
use Sherlockode\AdvancedContentBundle\LayoutType\LayoutTypeInterface;
use Sherlockode\AdvancedContentBundle\Manager\ConfigurationManager;
use Sherlockode\AdvancedContentBundle\Manager\ElementManager;
use Sherlockode\AdvancedContentBundle\Manager\UploadManager;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Contracts\Translation\TranslatorInterface;

class ElementImport
{
    /**
     * @var ElementManager
     */
    private $elementManager;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var ConfigurationManager
     */
    private $configurationManager;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var UploadManager
     */
    private $uploadManager;

    /**
     * @var string
     */
    private $rootDir;

    /**
     * @var string
     */
    private $filesDirectory;

    /**
     * @param ElementManager         $elementManager
     * @param EntityManagerInterface $em
     * @param ConfigurationManager   $configurationManager
     * @param TranslatorInterface    $translator
     * @param UploadManager          $uploadManager
     * @param string                 $rootDir
     */
    public function __construct(
        ElementManager $elementManager,
        EntityManagerInterface $em,
        ConfigurationManager $configurationManager,
        TranslatorInterface $translator,
        UploadManager $uploadManager,
        $rootDir
    ) {
        $this->elementManager = $elementManager;
        $this->em = $em;
        $this->configurationManager = $configurationManager;
        $this->translator = $translator;
        $this->uploadManager = $uploadManager;
        $this->rootDir = $rootDir;
    }

    public function getElementImportData(array $elementData, int $position = 0)
    {
        if (!isset($elementData['type'])) {
            throw new \Exception($this->translator->trans('init.errors.element_missing_type', [], 'AdvancedContentBundle'));
        }

        $element = $this->elementManager->getElementByCode($elementData['type']);
        if ($element instanceof FieldTypeInterface) {
            $data = $this->getFieldTypeImportData($element, $elementData);
        } elseif ($element instanceof LayoutTypeInterface) {
            $data = $this->getLayoutTypeImportData($element, $elementData);
        } else {
            throw new InvalidElementException(sprintf('Element of type "%s" is not handled in import', get_class($element)));
        }

        return array_merge([
            'elementType' => $elementData['type'],
            'position' => $position,
        ], $data);
    }

    private function getFieldTypeImportData(FieldTypeInterface $element, array $elementData)
    {
        $value = '';
        if ($element->getValueModelTransformer() !== null) {
            $value = [];
        }
        if (isset($elementData['value'])) {
            $value = $elementData['value'];
            if (is_array($value)) {
                $value = $this->processValueArray($value);
            }
        }

        return ['value' => $value];
    }

    private function getLayoutTypeImportData(LayoutTypeInterface $element, array $elementData)
    {
        $elements = $elementData['elements'] ?? [];
        $elementsData = [];
        $position = 0;
        foreach ($elements as $childElement) {
            $elementsData[] = $this->getElementImportData($childElement, $position++);
        }

        return ['elements' => $elementsData];
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
            if ($content === null) {
                throw new \Exception($this->translator->trans('init.errors.content_entity_not_found', [
                    '%slug%' => $slug,
                    '%locale%' => $locale,
                ], 'AdvancedContentBundle'));
            }
            $data = [
                'content' => $content->getId(),
            ];
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
        $fileName = $this->getFilesDirectory() . $data['_file'];
        if (!file_exists($fileName)) {
            throw new \Exception($this->translator->trans('init.errors.element_file_not_found', ['%file%' => $fileName], 'AdvancedContentBundle'));
        }

        $file = new File($fileName);
        $fileName = $this->uploadManager->copy($file);
        $data['src'] = $fileName;
        unset($data['_file']);

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
