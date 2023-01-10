<?php

namespace Sherlockode\AdvancedContentBundle\Import;

use Doctrine\ORM\EntityManagerInterface;
use Sherlockode\AdvancedContentBundle\Manager\ConfigurationManager;
use Sherlockode\AdvancedContentBundle\Model\ContentInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class ContentImport extends AbstractImport
{
    /**
     * @var ElementImport
     */
    private $elementImport;

    /**
     * @param EntityManagerInterface $em
     * @param ConfigurationManager   $configurationManager
     * @param TranslatorInterface    $translator
     * @param ElementImport          $elementImport
     */
    public function __construct(
        EntityManagerInterface $em,
        ConfigurationManager $configurationManager,
        TranslatorInterface $translator,
        ElementImport $elementImport
    ) {
        parent::__construct($em, $configurationManager, $translator);

        $this->elementImport = $elementImport;
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
            'locale' => $contentData['locale'] ?? null,
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
            $this->createElements($contentData['children'], $content);
        }

        $this->em->persist($content);
        $this->em->flush();
    }

    /**
     * @param array            $elementsData
     * @param ContentInterface $content
     */
    public function createElements(array $elementsData, ContentInterface $content)
    {
        $elements = [];
        $position = 0;
        foreach ($elementsData as $elementData) {
            try {
                $elements[] = $this->elementImport->getElementImportData($elementData, $position++);
            } catch (\Exception $e) {
                $this->errors[] = sprintf('%s : %s', $content->getName(), $e->getMessage());
            }
        }
        $content->setData($elements);
    }

    /**
     * @param string $dir
     */
    public function setFilesDirectory($dir)
    {
        $this->elementImport->setFilesDirectory($dir);
    }
}
