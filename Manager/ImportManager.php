<?php

namespace Sherlockode\AdvancedContentBundle\Manager;

use Sherlockode\AdvancedContentBundle\Import\ContentImport;
use Sherlockode\AdvancedContentBundle\Import\ContentTypeImport;
use Sherlockode\AdvancedContentBundle\Import\ImportResult;
use Sherlockode\AdvancedContentBundle\Import\PageImport;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Yaml\Yaml;
use Symfony\Contracts\Translation\TranslatorInterface;

class ImportManager
{
    const ENTITY_MAPPING = [
        'pages' => 'Page',
        'contents' => 'Content',
    ];

    /**
     * @var ContentImport
     */
    private $contentImport;

    /**
     * @var PageImport
     */
    private $pageImport;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var array
     */
    private $dataToProcess;

    /**
     * @var SymfonyStyle
     */
    private $symfonyStyle;

    /**
     * @param PageImport          $pageImport
     * @param ContentImport       $contentImport
     * @param TranslatorInterface $translator
     */
    public function __construct(
        PageImport $pageImport,
        ContentImport $contentImport,
        TranslatorInterface $translator
    ) {
        $this->pageImport = $pageImport;
        $this->contentImport = $contentImport;
        $this->pageImport->setContentImport($this->contentImport);
        $this->translator = $translator;

        $this->dataToProcess = [
            'Page' => [
                'service' => $this->pageImport,
                'data' => [],
            ],
            'Content' => [
                'service' => $this->contentImport,
                'data' => [],
            ],
        ];
    }

    /**
     * @param \SplFileInfo $file
     *
     * @throws \Exception
     */
    public function addFileToProcess(\SplFileInfo $file)
    {
        $filePath = $file->getRealPath();
        $data = Yaml::parseFile($filePath);

        foreach ($data as $entityType => $entities) {
            if (!isset(self::ENTITY_MAPPING[$entityType])) {
                throw new \Exception($this->translator->trans('init.errors.unknown_entity_type', ['%type%' => $entityType, '%list%' => join(', ', array_keys(self::ENTITY_MAPPING))], 'AdvancedContentBundle'));
            }

            foreach ($entities as $slug => $entityData) {
                $this->dataToProcess[self::ENTITY_MAPPING[$entityType]]['data'][$slug] = $entityData;
            }
        }
    }

    /**
     * @param array $allowedTypes
     *
     * @return array|ImportResult[]
     */
    public function processData($allowedTypes = [])
    {
        $results = [];
        foreach ($this->dataToProcess as $type => $dataToProcess) {
            if (count($allowedTypes) > 0 && !in_array($type, $allowedTypes)) {
                continue;
            }

            $nbEntities = count($dataToProcess['data']);
            if ($nbEntities === 0) {
                continue;
            }

            if ($this->symfonyStyle instanceof SymfonyStyle) {
                $this->symfonyStyle->title(
                    $this->translator->trans('init.title', ['%entity%' => $type], 'AdvancedContentBundle')
                );
                $this->symfonyStyle->progressStart($nbEntities);
            }

            foreach ($dataToProcess['data'] as $slug => $data) {
                if ($this->symfonyStyle instanceof SymfonyStyle) {
                    $this->symfonyStyle->progressAdvance();
                }

                /** @var ImportResult $result */
                $result = $dataToProcess['service']->importData($slug, $data);
                $results[] = $result;
                foreach ($result->getMessages() as $message) {
                    if ($this->symfonyStyle instanceof SymfonyStyle) {
                        $this->symfonyStyle->error($message);
                    }
                }
            }

            if ($this->symfonyStyle instanceof SymfonyStyle) {
                $this->symfonyStyle->progressFinish();
            }
        }

        return $results;
    }

    /**
     * @param bool $allowUpdate
     */
    public function setAllowUpdate($allowUpdate)
    {
        $this->pageImport->setAllowUpdate($allowUpdate);
        $this->contentImport->setAllowUpdate($allowUpdate);
    }

    /**
     * @param SymfonyStyle $symfonyStyle
     */
    public function setSymfonyStyle(SymfonyStyle $symfonyStyle)
    {
        $this->symfonyStyle = $symfonyStyle;
    }

    /**
     * @param string $dir
     */
    public function setFilesDirectory($dir)
    {
        $this->contentImport->setFilesDirectory($dir);
    }
}
