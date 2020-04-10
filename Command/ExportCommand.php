<?php

namespace Sherlockode\AdvancedContentBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use Sherlockode\AdvancedContentBundle\Manager\ConfigurationManager;
use Sherlockode\AdvancedContentBundle\Manager\ExportManager;
use Sherlockode\AdvancedContentBundle\Model\PageInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Contracts\Translation\TranslatorInterface;

class ExportCommand extends Command
{
    const AVAILABLE_ENTITIES = ['ContentType', 'Page', 'Content'];

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
     * @var ExportManager
     */
    private $exportManager;

    /**
     * @var array
     */
    private $exportTypes = [];

    /**
     * @param EntityManagerInterface $em
     * @param ConfigurationManager   $configurationManager
     * @param TranslatorInterface    $translator
     * @param ExportManager          $exportManager
     * @param string                 $rootDir
     * @param null|string            $name
     */
    public function __construct(
        EntityManagerInterface $em,
        ConfigurationManager $configurationManager,
        TranslatorInterface $translator,
        ExportManager $exportManager,
        $rootDir,
        $name = null
    ) {
        parent::__construct($name);
        $this->em = $em;
        $this->configurationManager = $configurationManager;
        $this->exportManager = $exportManager;
        $this->translator = $translator;
        $this->rootDir = $rootDir;
    }

    protected function configure()
    {
        $this
            ->setName('sherlockode:acb:export')
            ->setDescription('Export ACB content types, contents and pages')
            ->addOption(
                'type',
                't',
                InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY,
                'Type of entity to import.',
                self::AVAILABLE_ENTITIES
            )
            ->addOption(
                'dir',
                'd',
                InputOption::VALUE_OPTIONAL,
                'Directory in which the files will be exported'
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

            if (in_array('ContentType', $this->exportTypes)) {
                $contentTypes = $this->em->getRepository($this->configurationManager->getEntityClass('content_type'))->findAll();
                $this->exportManager->generateContentTypesData($contentTypes);
            }
            if (in_array('Page', $this->exportTypes)) {
                $pages = $this->em->getRepository($this->configurationManager->getEntityClass('page'))->findAll();
                $this->exportManager->generatePagesData($pages);
            }
            if (in_array('Content', $this->exportTypes)) {
                $contents = $this->em->getRepository($this->configurationManager->getEntityClass('content'))->findAll();
                $contentsToExport = [];
                foreach ($contents as $content) {
                    if ($content->getPage() instanceof PageInterface) {
                        continue;
                    }
                    $contentsToExport[] = $content;
                }
                $this->exportManager->generateContentsData($contentsToExport);
            }

            $this->exportManager->generateFiles($this->sourceDirectory);

            $this->symfonyStyle->success($this->translator->trans('init.export_success', ['%dir%' => $this->sourceDirectory], 'AdvancedContentBundle'));
        } catch (\Exception $e) {
            $this->symfonyStyle->error($e->getMessage());
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
        if (strpos($initDir, '/') !== 0) {
            $initDir = $this->rootDir . '/' . $initDir;
        }
        $initDir .= '/';

        if (!file_exists($initDir)) {
            throw new \Exception(
                $this->translator->trans('init.errors.init_dir', ['%dir%' => $initDir], 'AdvancedContentBundle')
            );
        }
        $this->sourceDirectory = $initDir;

        $exportTypes = $input->getOption('type');
        foreach ($exportTypes as $exportType) {
            if (!in_array($exportType, self::AVAILABLE_ENTITIES)) {
                throw new \Exception(
                    $this->translator->trans('init.errors.unknown_entity_type', ['%type%' => $exportType, '%list%' => join(', ', self::AVAILABLE_ENTITIES)], 'AdvancedContentBundle')
                );
            }
        }
        $this->exportTypes = $exportTypes;
    }
}
