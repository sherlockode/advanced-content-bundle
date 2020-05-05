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

        $this->em->getEventManager()->removeEventListener('postPersist', 'sherlockode_advanced_content.content_type_listener');
        $this->em->getEventManager()->removeEventListener('postUpdate', 'sherlockode_advanced_content.content_type_listener');
    }

    /**
     * @param string $slug
     * @param array  $pageData
     */
    protected function importEntity($slug, $pageData)
    {
        if (!isset($pageData['title'])) {
            $this->errors[] = $this->translator->trans('init.errors.page_missing_data', [], 'AdvancedContentBundle');

            return;
        }

        if (isset($pageData['title'])) {
            $title = $pageData['title'];
        } else {
            $title = $slug;
        }

        $page = $this->em->getRepository($this->entityClasses['page'])->findOneBy([
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

        $contentType = null;
        if (isset($pageData['contentType'])) {
            $contentType = $this->em->getRepository($this->entityClasses['content_type'])->findOneBy([
                'slug' => $pageData['contentType']
            ]);
            if (!$contentType instanceof ContentTypeInterface) {
                $this->errors[] = $this->translator->trans('init.errors.content_no_content_type_found', ['%slug%' => $pageData['contentType']], 'AdvancedContentBundle');

                return;
            }
            /** @var ContentTypeInterface $contentType */
            $contentType->setPage($page);
            $contentType->setPageType(null);
        } elseif ($pageType instanceof PageTypeInterface) {
            $contentType = $this->em->getRepository($this->entityClasses['content_type'])->findOneBy([
                'pageType' => $pageType,
            ]);
        }

        if (!$contentType instanceof ContentTypeInterface) {
            foreach ($page->getContents() as $content) {
                $page->removeContent($content);
                $this->em->remove($content);
            }
            if (!empty($pageData['contents'])) {
                $this->errors[] = $this->translator->trans('init.errors.page_no_content_type', ['%page%' => $pageData['title']], 'AdvancedContentBundle');
            }
        } else {
            foreach ($page->getContents() as $content) {
                if ($contentType->getId() !== $content->getContentType()->getId()) {
                    $page->removeContent($content);
                    $this->em->remove($content);
                }
            }

            if (!empty($pageData['contents'])) {
                $existingContents = [];
                foreach ($page->getContents() as $content) {
                    $existingContents[$content->getLocale()] = $content;
                }
                foreach ($pageData['contents'] as $locale => $contentData) {
                    if (isset($existingContents[$locale])) {
                        $content = $existingContents[$locale];
                    } else {
                        /** @var ContentInterface $content */
                        $content = new $this->entityClasses['content'];
                        $content->setContentType($contentType);
                        $content->setName($page->getTitle());
                        $content->setSlug($page->getSlug());
                        $content->setLocale($locale);
                        $page->addContent($content);
                        $this->em->persist($content);
                    }

                    $this->contentImport
                        ->resetErrors()
                        ->createFieldValues($contentData, $content);
                    $errors = $this->contentImport->getErrors();
                    foreach ($errors as $error) {
                        $this->errors[] = $error;
                    }
                }
            }
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
