<?php

namespace Sherlockode\AdvancedContentBundle\Command;

use Sherlockode\AdvancedContentBundle\Manager\ConfigurationManager;
use Sherlockode\AdvancedContentBundle\Manager\ImportManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Finder\Finder;
use Symfony\Contracts\Translation\TranslatorInterface;

class ImportCommand extends Command
{
    const AVAILABLE_ENTITIES = ['ContentType', 'Page', 'Content'];

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
     * @var ImportManager
     */
    private $importManager;

    /**
     * @var array
     */
    private $importTypes = [];

    /**
     * @var string
     */
    private $filename;

    /**
     * @param ConfigurationManager $configurationManager
     * @param TranslatorInterface  $translator
     * @param ImportManager        $importManager
     * @param string               $rootDir
     * @param null|string          $name
     */
    public function __construct(
        ConfigurationManager $configurationManager,
        TranslatorInterface $translator,
        ImportManager $importManager,
        $rootDir,
        $name = null
    ) {
        parent::__construct($name);
        $this->configurationManager = $configurationManager;
        $this->translator = $translator;
        $this->importManager = $importManager;
        $this->rootDir = $rootDir;
    }

    protected function configure()
    {
        $this
            ->setName('sherlockode:acb:import')
            ->setDescription('Create and update ACB content types, contents and pages')
            ->addOption(
                'type',
                't',
                InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY,
                'Type of entity to import.',
                self::AVAILABLE_ENTITIES
            )
            ->addOption(
                'file',
                'f',
                InputOption::VALUE_OPTIONAL,
                'Filename to import'
            )
            ->addOption(
                'dir',
                'd',
                InputOption::VALUE_OPTIONAL,
                'Directory in which the files to import are located'
            )
            ->addOption(
                'files-dir',
                null,
                InputOption::VALUE_OPTIONAL,
                'Directory in which the resource files to import are located (for file and image field types)'
            )
            ->addOption(
                'update',
                'u',
                InputOption::VALUE_NONE,
                'Use this option to force update (similar to init_command.allow_update configuration)'
            )
        ;
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
            $this->init($input);

            $this->addFilesToProcess();
            $this->importManager->setSymfonyStyle($this->symfonyStyle);
            $this->importManager->processData($this->importTypes);

        } catch (\Exception $e) {
            $this->symfonyStyle->error($e->getMessage());

            if (defined(sprintf('%s::FAILURE', get_class($this)))) {
                return self::FAILURE;
            }

            return;
        }

        if (defined(sprintf('%s::SUCCESS', get_class($this)))) {
            return self::SUCCESS;
        }
    }

    private function addFilesToProcess()
    {
        $finder = new Finder();
        $finder->files()->in($this->sourceDirectory);

        if ($this->filename !== null) {
            $finder->name($this->filename);
            if (!$finder->hasResults()) {
                $this->symfonyStyle->warning(
                    $this->translator->trans('init.errors.file_not_found', ['%dir%' => $this->sourceDirectory, '%file%' => $this->filename], 'AdvancedContentBundle')
                );

                return;
            }
        } else {
            $finder->name(['*.yaml', '*.yml']);
        }
        foreach ($finder as $file) {
            try {
                $this->importManager->addFileToProcess($file);
            } catch (\Exception $e) {
                $this->symfonyStyle->error($e->getMessage());
            }
        }
    }

    /**
     * @param InputInterface $input
     *
     * @throws \Exception
     */
    private function init(InputInterface $input)
    {
        $initDir = $input->getOption('dir');
        if ($initDir === null) {
            $initDir = $this->configurationManager->getInitDirectory();
        }
        $initDir = $this->getDirFullPath($initDir);
        $this->sourceDirectory = $initDir;

        $filesDir = $input->getOption('files-dir');
        if ($filesDir !== null) {
            $filesDir = $this->getDirFullPath($filesDir);
            $this->importManager->setFilesDirectory($filesDir);
        }

        $allowUpdate = $this->configurationManager->initCanUpdate();
        if ($input->getOption('update') === true) {
            $allowUpdate = true;
        }

        $this->importManager->setAllowUpdate($allowUpdate);

        $importTypes = $input->getOption('type');
        foreach ($importTypes as $importType) {
            if (!in_array($importType, self::AVAILABLE_ENTITIES)) {
                throw new \Exception(
                    $this->translator->trans('init.errors.unknown_entity_type', ['%type%' => $importType, '%list%' => join(', ', self::AVAILABLE_ENTITIES)], 'AdvancedContentBundle')
                );
            }
        }
        $this->importTypes = $importTypes;

        $this->filename = $input->getOption('file');
    }

    /**
     * @param string $dir
     *
     * @return string
     *
     * @throws \Exception
     */
    private function getDirFullPath($dir)
    {
        if (strpos($dir, '/') !== 0) {
            $dir = $this->rootDir . '/' . $dir;
        }
        $dir .= '/';

        if (!file_exists($dir)) {
            throw new \Exception(
                $this->translator->trans('init.errors.init_dir', ['%dir%' => $dir], 'AdvancedContentBundle')
            );
        }

        return $dir;
    }
}
