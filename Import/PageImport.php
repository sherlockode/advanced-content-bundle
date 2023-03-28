<?php

namespace Sherlockode\AdvancedContentBundle\Import;

use Sherlockode\AdvancedContentBundle\Model\ContentInterface;
use Sherlockode\AdvancedContentBundle\Model\PageInterface;
use Sherlockode\AdvancedContentBundle\Model\PageMetaInterface;
use Sherlockode\AdvancedContentBundle\Model\PageTypeInterface;

class PageImport extends AbstractImport
{
    /**
     * @var ContentImport
     */
    private $contentImport;

    /**
     * @param string $pageIdentifier
     * @param array  $pageData
     */
    protected function importEntity($pageIdentifier, $pageData)
    {
        if (empty($pageData['meta'])) {
            $this->errors[] = $this->translator->trans('init.errors.page_missing_metas', [], 'AdvancedContentBundle');

            return;
        }

        try {
            $scopes = $this->getScopesForEntity($pageData['scopes'] ?? []);
            $page = $this->getExistingScopableEntity($this->entityClasses['page'], ['pageIdentifier' => $pageIdentifier], $scopes);
        } catch (\Exception $e) {
            $this->errors[] = $e->getMessage();

            return;
        }

        if (!$page instanceof PageInterface) {
            $page = new $this->entityClasses['page'];
        } elseif (!$this->allowUpdate) {
            // Page already exist but update is not allowed by configuration
            return;
        }

        $page->setPageIdentifier($pageIdentifier);
        $page->setStatus($pageData['status'] ?? PageInterface::STATUS_DRAFT);
        $this->updateEntityScopes($page, $scopes);

        $pageType = null;
        if (isset($pageData['pageType'])) {
            $pageTypes = $this->em->getRepository($this->entityClasses['page_type'])->findBy([
                'name' => $pageData['pageType']
            ]);
            if (count($pageTypes) > 1) {
                $this->errors[] = $this->translator->trans('init.errors.page_type_too_many_matches', ['%name%' => $pageData['pageType']], 'AdvancedContentBundle');

                return;
            }

            $pageType = null;
            if (count($pageTypes) > 0) {
                $pageType = $pageTypes[0];
            }
            if (!$pageType instanceof PageTypeInterface) {
                /** @var PageTypeInterface $pageType */
                $pageType = new $this->entityClasses['page_type'];
                $pageType->setName($pageData['pageType']);
                $this->em->persist($pageType);
            }
        }
        $page->setPageType($pageType);

        if (!empty($pageData['content'])) {
            $contentData = $pageData['content'];
            $content = $page->getContent();
            if ($content === null) {
                /** @var ContentInterface $content */
                $content = new $this->entityClasses['content'];
                $content->setName($page->getPageIdentifier());
                $content->setSlug($page->getPageIdentifier());
                $page->setContent($content);
                $this->em->persist($content);
            }

            $this->contentImport
                ->resetErrors()
                ->createElements($contentData, $content);
            $errors = $this->contentImport->getErrors();
            foreach ($errors as $error) {
                $this->errors[] = $error;
            }
        }

        $pageMeta = $page->getPageMeta();
        $metaData = $pageData['meta'];
        if (!isset($metaData['title']) || !isset($metaData['slug'])) {
            $this->errors[] = $this->translator->trans('init.errors.page_missing_data', [], 'AdvancedContentBundle');

            return;
        }

        $title = $metaData['title'];
        $slug = $metaData['slug'];
        if ($pageMeta === null) {
            /** @var PageMetaInterface $pageMeta */
            $pageMeta = new $this->entityClasses['page_meta'];
            $page->setPageMeta($pageMeta);
        }
        $pageMeta->setTitle($title);
        $pageMeta->setSlug($slug);
        $pageMeta->setMetaTitle($metaData['meta_title'] ?? null);
        $pageMeta->setMetaDescription($metaData['meta_description'] ?? null);

        if (!$this->scopeHandler->isPageIdentifierValid($page)) {
            if ($this->configurationManager->isScopesEnabled()) {
                $this->errors[] = $this->translator->trans('page.errors.duplicate_identifier_scopes', [], 'AdvancedContentBundle');
            } else {
                $this->errors[] = $this->translator->trans('page.errors.duplicate_identifier_no_scope', [], 'AdvancedContentBundle');
            }
            return;
        }
        if (!$this->scopeHandler->isPageSlugValid($page)) {
            if ($this->configurationManager->isScopesEnabled()) {
                $this->errors[] = $this->translator->trans('page.errors.duplicate_slug_scopes', [], 'AdvancedContentBundle');
            } else {
                $this->errors[] = $this->translator->trans('page.errors.duplicate_slug_no_scope', [], 'AdvancedContentBundle');
            }
            return;
        }

        $this->em->persist($page);
        $this->em->flush();
    }

    /**
     * @param ContentImport $contentImport
     *
     * @return $this
     */
    public function setContentImport(ContentImport $contentImport)
    {
        $this->contentImport = $contentImport;

        return $this;
    }
}
