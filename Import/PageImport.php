<?php

namespace Sherlockode\AdvancedContentBundle\Import;

use Sherlockode\AdvancedContentBundle\Model\ContentInterface;
use Sherlockode\AdvancedContentBundle\Model\ContentTypeInterface;
use Sherlockode\AdvancedContentBundle\Model\PageInterface;
use Sherlockode\AdvancedContentBundle\Model\PageTypeInterface;

class PageImport extends AbstractImport
{
    /**
     * @var ContentImport
     */
    private $contentImport;

    protected function init()
    {
        parent::init();

        $this->om->getEventManager()->removeEventListener('postPersist', 'sherlockode_advanced_content.content_type_listener');
        $this->om->getEventManager()->removeEventListener('postUpdate', 'sherlockode_advanced_content.content_type_listener');
    }

    /**
     * @param array $pageData
     */
    protected function importData($pageData)
    {
        if (!isset($pageData['name']) && !isset($pageData['slug'])) {
            $this->errors[] = $this->translator->trans('init.errors.page_missing_data', [], 'AdvancedContentBundle');

            return;
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
            return;
        }

        $page->setTitle($title);
        $page->setSlug($slug);
        $page->setStatus($pageData['status'] ?? PageInterface::STATUS_DRAFT);
        $page->setMetaDescription($pageData['meta'] ?? '');

        $pageType = null;
        if (isset($pageData['pageType'])) {
            $pageTypes = $this->om->getRepository($this->entityClasses['page_type'])->findBy([
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
                $this->om->persist($pageType);
            }
        }
        $page->setPageType($pageType);

        $contentType = null;
        if (isset($pageData['contentType'])) {
            $contentTypes = $this->om->getRepository($this->entityClasses['content_type'])->findBy([
                'name' => $pageData['contentType']
            ]);
            if (count($contentTypes) !== 1) {
                $transKey = count($contentTypes) > 1 ? 'content_type_too_many_matches' : 'content_no_content_type_found';
                $this->errors[] = $this->translator->trans('init.errors.' . $transKey, ['%name%' => $pageData['contentType']], 'AdvancedContentBundle');

                return;
            }
            /** @var ContentTypeInterface $contentType */
            $contentType = $contentTypes[0];
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
                $this->errors[] = $this->translator->trans('init.errors.page_no_content_type', ['%page%' => $pageData['title']], 'AdvancedContentBundle');
            }
        } else {
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
                $this->contentImport
                    ->resetErrors()
                    ->createFieldValues($pageData['children'], $page->getContent());
                $errors = $this->contentImport->getErrors();
                foreach ($errors as $error) {
                    $this->errors[] = $error;
                }
            }
        }

        $this->om->persist($page);
        $this->om->flush();
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
