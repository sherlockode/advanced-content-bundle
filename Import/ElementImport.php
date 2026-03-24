<?php

declare(strict_types=1);

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
     * @var string
     */
    private $filesDirectory;

    /**
     * @param string $rootDir
     */
    public function __construct(
        private readonly ElementManager $elementManager,
        private readonly EntityManagerInterface $em,
        private readonly ConfigurationManager $configurationManager,
        private readonly TranslatorInterface $translator,
        private readonly UploadManager $uploadManager,
        private $rootDir,
    ) {
    }

    public function getElementImportData(array $elementData, int $position = 0): array
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
            throw new InvalidElementException(sprintf('Element of type "%s" is not handled in import', $element::class));
        }

        return array_merge([
            'elementType' => $elementData['type'],
            'position' => $position,
            'extra' => $elementData['extra'] ?? [],
        ], $data);
    }

    private function getFieldTypeImportData(FieldTypeInterface $element, array $elementData): array
    {
        $value = '';
        if (null !== $element->getValueModelTransformer()) {
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

    private function getLayoutTypeImportData(LayoutTypeInterface $element, array $elementData): array
    {
        $elements = $elementData['elements'] ?? [];
        $elementsData = [];
        $position = 0;
        foreach ($elements as $childElement) {
            $elementsData[] = $this->getElementImportData($childElement, $position++);
        }

        return [
            'elements' => $elementsData,
            'config' => $elementData['config'] ?? [],
        ];
    }

    /**
     * @return mixed[]
     */
    private function processValueArray(array $data): array
    {
        if (isset($data['_file'])) {
            // handle file
            $result = $this->processFileUpload($data);
            if (false !== $result) {
                $data = $result;
            }
        }

        if (isset($data['content'])) {
            $slug = $data['content'];
            $content = $this->em->getRepository($this->configurationManager->getEntityClass('content'))->findOneBy([
                'slug' => $slug,
            ]);
            if (null === $content) {
                throw new \Exception($this->translator->trans('init.errors.content_entity_not_found', ['%slug%' => $slug], 'AdvancedContentBundle'));
            }
        }

        // browse array
        $newData = [];
        foreach ($data as $key => $valueEntry) {
            $newData[$key] = is_array($valueEntry) ? $this->processValueArray($valueEntry) : $valueEntry;
        }

        return $newData;
    }

    private function processFileUpload(array $data): array
    {
        $fileName = $this->getFilesDirectory().$data['_file'];
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
        if (null === $this->filesDirectory) {
            $filesDirectory = $this->configurationManager->getInitFilesDirectory();
            if (!str_starts_with($filesDirectory, '/')) {
                $filesDirectory = $this->rootDir.'/'.$filesDirectory;
            }

            if (!file_exists($filesDirectory)) {
                throw new \Exception($this->translator->trans('init.errors.init_dir', ['%dir%' => $filesDirectory], 'AdvancedContentBundle'));
            }

            $this->filesDirectory = $filesDirectory.'/';
        }

        return $this->filesDirectory;
    }

    /**
     * @param string $dir
     */
    public function setFilesDirectory($dir): void
    {
        $this->filesDirectory = $dir;
    }
}
