<?php

declare(strict_types=1);

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
    public const AVAILABLE_ENTITIES = ['Page', 'Content'];

    private ?SymfonyStyle $symfonyStyle = null;

    private ?string $sourceDirectory = null;

    /**
     * @var array
     */
    private $exportTypes = [];

    /**
     * @param string $rootDir
     */
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly ConfigurationManager $configurationManager,
        private readonly TranslatorInterface $translator,
        private readonly ExportManager $exportManager,
        private $rootDir,
        ?string $name = null,
    ) {
        parent::__construct($name);
    }

    protected function configure()
    {
        $this
            ->setName('sherlockode:acb:export')
            ->setDescription('Export ACB contents and pages')
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
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->symfonyStyle = new SymfonyStyle($input, $output);
        try {
            $this->init($input);

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
        } catch (\Exception $exception) {
            $this->symfonyStyle->error($exception->getMessage());

            if (defined(sprintf('%s::FAILURE', static::class))) {
                return self::FAILURE;
            }

            return null;
        }

        if (defined(sprintf('%s::SUCCESS', static::class))) {
            return self::SUCCESS;
        }

        return null;
    }

    /**
     * @throws \Exception
     */
    private function init(InputInterface $input): void
    {
        $initDir = $input->getOption('dir');
        if (null === $initDir) {
            $initDir = $this->configurationManager->getInitDirectory();
        }

        if (!str_starts_with((string) $initDir, '/')) {
            $initDir = $this->rootDir.'/'.$initDir;
        }

        $initDir .= '/';

        if (!file_exists($initDir)) {
            throw new \Exception($this->translator->trans('init.errors.init_dir', ['%dir%' => $initDir], 'AdvancedContentBundle'));
        }

        $this->sourceDirectory = $initDir;

        $exportTypes = $input->getOption('type');
        foreach ($exportTypes as $exportType) {
            if (!in_array($exportType, self::AVAILABLE_ENTITIES)) {
                throw new \Exception($this->translator->trans('init.errors.unknown_entity_type', ['%type%' => $exportType, '%list%' => implode(', ', self::AVAILABLE_ENTITIES)], 'AdvancedContentBundle'));
            }
        }

        $this->exportTypes = $exportTypes;
    }
}
