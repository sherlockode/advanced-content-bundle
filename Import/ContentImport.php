<?php

namespace Sherlockode\AdvancedContentBundle\Import;

use Doctrine\ORM\EntityManagerInterface;
use Sherlockode\AdvancedContentBundle\Manager\ConfigurationManager;
use Sherlockode\AdvancedContentBundle\Model\ContentInterface;
use Sherlockode\AdvancedContentBundle\Scope\ScopeHandlerInterface;
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
     * @param ScopeHandlerInterface  $scopeHandler
     * @param ElementImport          $elementImport
     */
    public function __construct(
        EntityManagerInterface $em,
        ConfigurationManager $configurationManager,
        TranslatorInterface $translator,
        ScopeHandlerInterface $scopeHandler,
        ElementImport $elementImport
    ) {
        parent::__construct($em, $configurationManager, $translator, $scopeHandler);

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

        try {
            $scopes = $this->getScopesForEntity($contentData['scopes'] ?? []);
            $content = $this->getExistingScopableEntity($this->entityClasses['content'], ['slug' => $slug], $scopes);
        } catch (\Exception $e) {
            $this->errors[] = $e->getMessage();

            return;
        }

        if (!$content instanceof ContentInterface) {
            $content = new $this->entityClasses['content'];
        } elseif (!$this->allowUpdate) {
            // Content already exist but update is not allowed by configuration
            return;
        }

        $content->setSlug($slug);
        $content->setName($contentData['name']);
        $this->updateEntityScopes($content, $scopes);
        if (isset($contentData['children'])) {
            $this->createElements($contentData['children'], $content);
        }

        if (!$this->scopeHandler->isContentSlugValid($content)) {
            if ($this->configurationManager->isScopesEnabled()) {
                $this->errors[] = $this->translator->trans('content.errors.duplicate_slug_scopes', [], 'AdvancedContentBundle');
            } else {
                $this->errors[] = $this->translator->trans('content.errors.duplicate_slug_no_scope', [], 'AdvancedContentBundle');
            }
            return;
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
