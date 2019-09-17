<?php

namespace Sherlockode\AdvancedContentBundle\Command;

use Doctrine\Common\Persistence\ObjectManager;
use Sherlockode\AdvancedContentBundle\Import\AbstractImport;
use Sherlockode\AdvancedContentBundle\Import\ContentImport;
use Sherlockode\AdvancedContentBundle\Import\ContentTypeImport;
use Sherlockode\AdvancedContentBundle\Import\PageImport;
use Sherlockode\AdvancedContentBundle\Manager\ConfigurationManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Finder\Finder;
use Symfony\Contracts\Translation\TranslatorInterface;

class AcbInitCommand extends Command
{
    /**
     * @var ObjectManager
     */
    private $om;

    /**
     * @var ConfigurationManager
     */
    private $configurationManager;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var string
     */
    private $rootDir;

    /**
     * @var SymfonyStyle
     */
    private $symfonyStyle;

    /**
     * @var string
     */
    private $sourceDirectory;

    /**
     * @var bool
     */
    private $allowUpdate;

    /**
     * @var ContentTypeImport
     */
    private $contentTypeImport;

    /**
     * @var PageImport
     */
    private $pageImport;

    /**
     * @var ContentImport
     */
    private $contentImport;

    /**
     * @param ObjectManager        $om
     * @param ConfigurationManager $configurationManager
     * @param TranslatorInterface  $translator
     * @param ContentTypeImport    $contentTypeImport
     * @param PageImport           $pageImport
     * @param ContentImport        $contentImport
     * @param string               $rootDir
     * @param null|string          $name
     */
    public function __construct(
        ObjectManager $om,
        ConfigurationManager $configurationManager,
        TranslatorInterface $translator,
        ContentTypeImport $contentTypeImport,
        PageImport $pageImport,
        ContentImport $contentImport,
        $rootDir,
        $name = null
    ) {
        parent::__construct($name);
        $this->om = $om;
        $this->configurationManager = $configurationManager;
        $this->contentTypeImport = $contentTypeImport;
        $this->pageImport = $pageImport;
        $this->contentImport = $contentImport;
        $this->translator = $translator;
        $this->rootDir = $rootDir;
    }

    protected function configure()
    {
        $this
            ->setName('sherlockode:acb:init')
            ->setDescription('Create and update ACB content types, contents and pages');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->symfonyStyle = new SymfonyStyle($input, $output);
        try {
            $this->init();
            $this->createContentTypes();
            $this->createPages();
            $this->createContents();
        } catch (\Exception $e) {
            $this->symfonyStyle->error($e->getMessage());
        }
    }

    private function createContentTypes()
    {
        $this->createEntities('ContentType', $this->contentTypeImport);
    }

    private function createPages()
    {
        $this->createEntities('Page', $this->pageImport);
    }

    private function createContents()
    {
        $this->createEntities('Content', $this->contentImport);
    }

    /**
     * @param string         $entityType
     * @param AbstractImport $import
     */
    private function createEntities($entityType, AbstractImport $import)
    {
        $files = $this->getEntityFilesContent($entityType);
        $nbFiles = count($files);
        if ($nbFiles === 0) {
            return;
        }

        $this->symfonyStyle->title(
            $this->translator->trans('init.title', ['%entity%' => $entityType], 'AdvancedContentBundle')
        );
        $this->symfonyStyle->progressStart($nbFiles);

        foreach ($files as $file) {
            $this->symfonyStyle->progressAdvance();

            $result = $import->importFile($file);
            foreach ($result->getMessages() as $message) {
                $this->symfonyStyle->error($message);
            }
        }
        $this->symfonyStyle->progressFinish();
    }

    /**
     * @param string $entityType
     *
     * @return array
     */
    private function getEntityFilesContent($entityType)
    {
        $dir = $this->sourceDirectory . $entityType;
        if (!file_exists($dir)) {
            $this->symfonyStyle->warning(
                $this->translator->trans('init.errors.entity_dir', ['%dir%' => $dir, '%entity%' => $entityType], 'AdvancedContentBundle')
            );

            return [];
        }

        $files = [];
        $finder = new Finder();
        $finder->files()->in($dir);
        foreach ($finder as $file) {
            $files[] = $file;
        }

        return $files;
    }

    /**
     * @throws \Exception
     */
    private function init()
    {
        $initDir = $this->configurationManager->getInitDirectory();
        $initDir = trim($initDir, " \t\n\r\0\x0B/");
        $initDir = $this->rootDir . '/' . $initDir . '/';

        if (!file_exists($initDir)) {
            throw new \Exception(
                $this->translator->trans('init.errors.init_dir', ['%dir%' => $initDir], 'AdvancedContentBundle')
            );
        }

        $this->sourceDirectory = $initDir;
        $this->allowUpdate = $this->configurationManager->initCanUpdate();

        $filesDirectory = $this->configurationManager->getInitFilesDirectory();
        if (substr($filesDirectory, 0, 1) !== '/') {
            $filesDirectory = $this->rootDir . '/' . $filesDirectory;
        }
        if (!file_exists($filesDirectory)) {
            throw new \Exception(
                $this->translator->trans('init.errors.init_dir', ['%dir%' => $filesDirectory], 'AdvancedContentBundle')
            );
        }

        $this->contentTypeImport->setAllowUpdate($this->allowUpdate);
        $this->contentImport
            ->setAllowUpdate($this->allowUpdate)
            ->setFilesDirectory($filesDirectory)
        ;
        $this->pageImport
            ->setAllowUpdate($this->allowUpdate)
            ->setContentImport($this->contentImport)
        ;
    }
}
