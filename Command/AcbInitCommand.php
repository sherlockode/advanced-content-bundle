<?php

namespace Sherlockode\AdvancedContentBundle\Command;

use Cocur\Slugify\Slugify;
use Doctrine\Common\Persistence\ObjectManager;
use Sherlockode\AdvancedContentBundle\Manager\ConfigurationManager;
use Sherlockode\AdvancedContentBundle\Model\ContentInterface;
use Sherlockode\AdvancedContentBundle\Model\ContentTypeInterface;
use Sherlockode\AdvancedContentBundle\Model\FieldGroupValueInterface;
use Sherlockode\AdvancedContentBundle\Model\FieldInterface;
use Sherlockode\AdvancedContentBundle\Model\FieldValueInterface;
use Sherlockode\AdvancedContentBundle\Model\LayoutInterface;
use Sherlockode\AdvancedContentBundle\Model\PageInterface;
use Sherlockode\AdvancedContentBundle\Model\PageTypeInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;
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
     * @var Slugify
     */
    private $slugify;

    /**
     * @var SymfonyStyle
     */
    private $symfonyStyle;

    /**
     * @var bool
     */
    private $defaultRequired = true;

    /**
     * @var string
     */
    private $sourceDirectory;

    /**
     * @var bool
     */
    private $allowUpdate;

    /**
     * @var array
     */
    private $entityClasses = [];

    /**
     * @param ObjectManager        $om
     * @param ConfigurationManager $configurationManager
     * @param TranslatorInterface  $translator
     * @param string               $rootDir
     * @param null|string          $name
     */
    public function __construct(
        ObjectManager $om,
        ConfigurationManager $configurationManager,
        TranslatorInterface $translator,
        $rootDir,
        $name = null
    ) {
        parent::__construct($name);
        $this->om = $om;
        $this->configurationManager = $configurationManager;
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
        } catch (\Exception $e) {
            $this->symfonyStyle->error($e->getMessage());
        }
    }

    private function createContentTypes()
    {
        $contentTypes = $this->getEntityFilesContent('ContentType');
        $nbContentTypes = count($contentTypes);
        if ($nbContentTypes === 0) {
            return;
        }

        $this->symfonyStyle->title(
            $this->translator->trans('init.title', ['%entity%' => 'ContentType'], 'AdvancedContentBundle')
        );
        $this->symfonyStyle->progressStart($nbContentTypes);

        $pagesTypes = [];
        foreach ($contentTypes as $contentTypeData) {
            $this->symfonyStyle->progressAdvance();

            if (!isset($contentTypeData['name'])) {
                $this->symfonyStyle->error(
                    $this->translator->trans('init.errors.contact_type_missing_name', [], 'AdvancedContentBundle')
                );
                continue;
            }

            $contentType = $this->om->getRepository($this->entityClasses['content_type'])->findOneBy([
                'name' => $contentTypeData['name']
            ]);
            if (!$contentType instanceof ContentTypeInterface) {
                $contentType = new $this->entityClasses['content_type'];
            } elseif (!$this->allowUpdate) {
                // ContentType already exist but update is not allowed by configuration
                continue;
            }
            $contentType->setName($contentTypeData['name']);

            if (isset($contentTypeData['children'])) {
                $this->createFields($contentTypeData['children'], $contentType);
            }

            if (isset($contentTypeData['pageType'])) {
                $pageType = $this->om->getRepository($this->entityClasses['page_type'])->findOneBy([
                    'name' => $contentTypeData['pageType']
                ]);
                if (!$pageType instanceof PageTypeInterface) {
                    if (isset($pagesTypes[$contentTypeData['pageType']])) {
                        $pageType = $pagesTypes[$contentTypeData['pageType']];
                    } else {
                        /** @var PageTypeInterface $pageType */
                        $pageType = new $this->entityClasses['page_type'];
                        $pageType->setName($contentTypeData['pageType']);
                        $this->om->persist($pageType);
                        $pagesTypes[$contentTypeData['pageType']] = $pageType;
                    }
                }
                $contentType->setPageType($pageType);
                $contentType->setPage(null);
            }

            $this->om->persist($contentType);
        }
        $this->om->flush();
        $this->symfonyStyle->progressFinish();
    }

    /**
     * @param array                $fields
     * @param ContentTypeInterface $contentType
     * @param LayoutInterface|null $layout
     */
    private function createFields($fields, ContentTypeInterface $contentType, LayoutInterface $layout = null)
    {
        $slugs = [];
        $position = 1;
        foreach ($fields as $fieldData) {
            if (!isset($fieldData['name']) || !isset($fieldData['type'])) {
                $this->symfonyStyle->error(
                    $this->translator->trans('init.errors.field_missing_data', [], 'AdvancedContentBundle')
                );
                continue;
            }

            $slug = $this->slugify->slugify($fieldData['name']);
            if (isset($fieldData['slug'])) {
                $slug = $fieldData['slug'];
            }
            if (in_array($slug, $slugs)) {
                $this->symfonyStyle->error(
                    $this->translator->trans('field_type.errors.duplicated_slug_detail', ['%slug%' => $slug], 'AdvancedContentBundle')
                );
                continue;
            }
            $slugs[] = $slug;

            if ($layout !== null) {
                $field = $this->om->getRepository($this->entityClasses['field'])->findOneBy([
                    'slug'   => $slug,
                    'layout' => $layout,
                ]);
                if (!$field instanceof FieldInterface) {
                    $field = new $this->entityClasses['field'];
                    $layout->addChild($field);
                }
            } else {
                $field = $this->om->getRepository($this->entityClasses['field'])->findOneBy([
                    'slug'        => $slug,
                    'contentType' => $contentType,
                ]);
                if (!$field instanceof FieldInterface) {
                    $field = new $this->entityClasses['field'];
                    $contentType->addField($field);
                }
            }

            $field->setName($fieldData['name']);
            $field->setSlug($slug);
            $field->setRequired($fieldData['required'] ?? $this->defaultRequired);
            $field->setType($fieldData['type']);
            $field->setPosition($position++);

            if (isset($fieldData['options'])) {
                $field->setOptions($fieldData['options']);
            }

            $this->om->persist($field);

            if (isset($fieldData['children'])) {
                if ($fieldData['type'] === 'repeater') {
                    $childLayout = $this->createLayout($fieldData['name'], 1, $field);
                    $this->createFields($fieldData['children'], $contentType, $childLayout);
                } elseif ($fieldData['type'] === 'flexible') {
                    $layoutPosition = 1;
                    foreach ($fieldData['children'] as $child) {
                        if (!isset($child['name'])) {
                            $this->symfonyStyle->error(
                                $this->translator->trans('init.errors.layout_missing_name', [], 'AdvancedContentBundle')
                            );
                            continue;
                        }
                        $childLayout = $this->createLayout($child['name'], $layoutPosition++, $field);
                        if (isset($child['children'])) {
                            $this->createFields($child['children'], $contentType, $childLayout);
                        }
                    }
                }
            }
        }
    }

    /**
     * @param string          $name
     * @param int             $position
     * @param FieldInterface  $field
     *
     * @return LayoutInterface
     */
    private function createLayout($name, $position, FieldInterface $field)
    {
        $layout = $this->om->getRepository($this->entityClasses['layout'])->findOneBy([
            'name' => $name,
            'parent' => $field,
        ]);
        if (!$layout instanceof LayoutInterface) {
            $layout = new $this->entityClasses['layout'];
        }
        $layout->setName($name);
        $layout->setParent($field);
        $layout->setPosition($position);
        $this->om->persist($layout);

        return $layout;
    }

    private function createPages()
    {
        $pages = $this->getEntityFilesContent('Page');
        $nbPages = count($pages);
        if ($nbPages === 0) {
            return;
        }

        $this->symfonyStyle->title(
            $this->translator->trans('init.title', ['%entity%' => 'Page'], 'AdvancedContentBundle')
        );
        $this->symfonyStyle->progressStart($nbPages);

        $pagesTypes = [];
        foreach ($pages as $pageData) {
            $this->symfonyStyle->progressAdvance();

            if (!isset($pageData['name']) && !isset($pageData['slug'])) {
                $this->symfonyStyle->error(
                    $this->translator->trans('init.errors.page_missing_data', [], 'AdvancedContentBundle')
                );
                continue;
            }

            if (isset($pageData['slug'])) {
                $slug = $pageData['slug'];
            } else {
                $slug = $this->slugify->slugify($pageData['name']);
            }

            if (isset($pageData['title'])) {
                $title = $pageData['title'];
            } else {
                $title = $pageData['slug'];
            }

            $page = $this->om->getRepository($this->entityClasses['page'])->findOneBy([
                'slug' => $slug,
            ]);
            if (!$page instanceof PageInterface) {
                $page = new $this->entityClasses['page'];
            } elseif (!$this->allowUpdate) {
                // Page already exist but update is not allowed by configuration
                continue;
            }

            $page->setTitle($title);
            $page->setSlug($slug);
            $page->setStatus($pageData['status'] ?? PageInterface::STATUS_DRAFT);
            $page->setMetaDescription($pageData['meta'] ?? '');

            $pageType = null;
            if (isset($pageData['pageType'])) {
                $pageType = $this->om->getRepository($this->entityClasses['page_type'])->findOneBy([
                    'name' => $pageData['pageType']
                ]);
                if (!$pageType instanceof PageTypeInterface) {
                    if (isset($pagesTypes[$pageData['pageType']])) {
                        $pageType = $pagesTypes[$pageData['pageType']];
                    } else {
                        /** @var PageTypeInterface $pageType */
                        $pageType = new $this->entityClasses['page_type'];
                        $pageType->setName($pageData['pageType']);
                        $this->om->persist($pageType);
                        $pagesTypes[$pageData['pageType']] = $pageType;
                    }
                }
            }
            $page->setPageType($pageType);

            $contentType = null;
            if (isset($pageData['contentType'])) {
                $contentType = $this->om->getRepository($this->entityClasses['content_type'])->findOneBy([
                    'name' => $pageData['contentType'],
                ]);
                if (!$contentType instanceof ContentTypeInterface) {
                    $this->symfonyStyle->error(
                        $this->translator->trans('init.errors.entity_not_found', ['%entity%' => 'ContentType', '%name%' => $pageData['contentType']], 'AdvancedContentBundle')
                    );
                    continue;
                }
                $contentType->setPage($page);
                $contentType->setPageType(null);
            } elseif ($pageType instanceof PageTypeInterface) {
                $contentType = $this->om->getRepository($this->entityClasses['content_type'])->findOneBy([
                    'pageType' => $pageType,
                ]);
            }

            if (!$contentType instanceof ContentTypeInterface) {
                if ($page->getContent() instanceof ContentInterface) {
                    $this->om->remove($page->getContent());
                    $page->setContent(null);
                }
                if (isset($pageData['children'])) {
                    $this->symfonyStyle->error(
                        $this->translator->trans('init.errors.page_no_content_type', ['%page%' => $pageData['title']], 'AdvancedContentBundle')
                    );
                }
            }

            if ($contentType instanceof ContentTypeInterface) {
                if (!$page->getContent() instanceof ContentInterface || $contentType->getId() !== $page->getContent()->getContentType()->getId()) {
                    if ($page->getContent() instanceof ContentInterface) {
                        $this->om->remove($page->getContent());
                    }

                    /** @var ContentInterface $content */
                    $content = new $this->entityClasses['content'];
                    $content->setContentType($contentType);
                    $page->setContent($content);
                    $this->om->persist($content);
                }
                if (isset($pageData['children'])) {
                    $this->createContent($pageData['children'], $page->getContent());
                }
            }

            $this->om->persist($page);
        }
        $this->om->flush();
        $this->symfonyStyle->progressFinish();
    }

    /**
     * @param array                         $fieldValuesData
     * @param ContentInterface              $content
     * @param LayoutInterface|null          $layout
     * @param FieldGroupValueInterface|null $fieldGroupValue
     */
    private function createContent($fieldValuesData, ContentInterface $content, LayoutInterface $layout = null, FieldGroupValueInterface $fieldGroupValue = null)
    {
        foreach ($fieldValuesData as $fieldValueData) {
            if (!isset($fieldValueData['slug'])) {
                $this->symfonyStyle->error(
                    $this->translator->trans('init.errors.field_value_missing_slug', [], 'AdvancedContentBundle')
                );
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
                $this->symfonyStyle->error(
                    $this->translator->trans('init.errors.entity_not_found', ['%entity%' => 'Field', '%name%' => $slug], 'AdvancedContentBundle')
                );
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
                if (is_array($fieldValueData['value'])) {
                    $fieldValueValue = serialize($fieldValueData['value']);
                } else {
                    $fieldValueValue = $fieldValueData['value'];
                }
            }
            $fieldValue->setValue($fieldValueValue);

            $this->om->persist($fieldValue);

            if (isset($fieldValueData['children'])) {
                $childFieldGroupPosition = 1;
                foreach ($fieldValueData['children'] as $fieldChildValues) {
                    if (!isset($fieldChildValues['name'])) {
                        $this->symfonyStyle->error(
                            $this->translator->trans('init.errors.field_group_value_missing_name', [], 'AdvancedContentBundle')
                        );
                        continue;
                    }
                    $fieldChildName = $fieldChildValues['name'];

                    $childLayout = $this->om->getRepository($this->entityClasses['layout'])->findOneBy([
                        'parent' => $field,
                        'name' => $fieldChildName,
                    ]);
                    if (!$childLayout instanceof LayoutInterface) {
                        $this->symfonyStyle->error(
                            $this->translator->trans('init.errors.entity_not_found', ['%entity%' => 'Layout', '%name%' => $fieldChildName], 'AdvancedContentBundle')
                        );
                        continue;
                    }

                    $childFieldGroupValue = $this->om->getRepository($this->entityClasses['field_group_value'])->findOneBy([
                        'parent' => $fieldValue,
                        'layout' => $childLayout,
                    ]);
                    if (!$childFieldGroupValue instanceof FieldGroupValueInterface) {
                        /** @var FieldGroupValueInterface $childFieldGroupValue */
                        $childFieldGroupValue = new $this->entityClasses['field_group_value'];
                        $childFieldGroupValue->setParent($fieldValue);
                        $childFieldGroupValue->setLayout($childLayout);
                        $this->om->persist($childFieldGroupValue);
                    }
                    $childFieldGroupValue->setPosition($childFieldGroupPosition++);

                    if (isset($fieldChildValues['children'])) {
                        $this->createContent($fieldChildValues['children'], $content, $childLayout, $childFieldGroupValue);
                    }
                }
            }
        }
    }

    /**
     * @param string $entityType
     *
     * @return array
     */
    private function getEntityFilesContent($entityType)
    {
        $entities = [];

        $dir = $this->sourceDirectory . $entityType;
        if (!file_exists($dir)) {
            $this->symfonyStyle->warning(
                $this->translator->trans('init.errors.entity_dir', ['%dir%' => $dir, '%entity%' => $entityType], 'AdvancedContentBundle')
            );

            return $entities;
        }

        $finder = new Finder();
        $finder->files()->in($dir);
        foreach ($finder as $file) {
            try {
                $filePath = $file->getRealPath();
                $entities[] = Yaml::parseFile($filePath);
            } catch (ParseException $e) {
                $this->symfonyStyle->error($e->getMessage());
            }
        }

        return $entities;
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
        $this->entityClasses = $this->configurationManager->getEntityClasses();
        $this->slugify = new Slugify();

        $this->om->getEventManager()->removeEventListener('postPersist', 'sherlockode_advanced_content.content_type_listener');
        $this->om->getEventManager()->removeEventListener('postUpdate', 'sherlockode_advanced_content.content_type_listener');
    }
}
