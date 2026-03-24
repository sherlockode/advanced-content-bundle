<?php

namespace Sherlockode\AdvancedContentBundle\Manager;

use Sherlockode\AdvancedContentBundle\Model\ContentInterface;
use Sherlockode\AdvancedContentBundle\Model\ContentVersionInterface;
use Sherlockode\AdvancedContentBundle\Model\PageInterface;
use Sherlockode\AdvancedContentBundle\Model\PageMetaInterface;
use Sherlockode\AdvancedContentBundle\Model\PageMetaVersionInterface;
use Sherlockode\AdvancedContentBundle\Model\PageVersionInterface;
use Sherlockode\AdvancedContentBundle\Model\VersionInterface;
use Sherlockode\AdvancedContentBundle\User\UserProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class VersionManager
{
    /**
     * @var ConfigurationManager
     */
    private $configurationManager;

    /**
     * @var UserProviderInterface
     */
    private $userProvider;

    /**
     * @var RequestStack
     */
    private $requestStack;

    public function __construct(
        ConfigurationManager $configurationManager,
        UserProviderInterface $userProvider,
        RequestStack $requestStack,
    ) {
        $this->configurationManager = $configurationManager;
        $this->userProvider = $userProvider;
        $this->requestStack = $requestStack;
    }

    public function getContentData(ContentInterface $content): array
    {
        if (null === $content->getPage() && $mainRequest = $this->getRequest()) {
            if ($contentVersionId = $mainRequest->get('versionId')) {
                foreach ($content->getVersions() as $version) {
                    if ($version->getId() === (int) $contentVersionId) {
                        return $version->getData();
                    }
                }
            }
        }

        if ($content->getContentVersion() instanceof ContentVersionInterface && !empty($content->getContentVersion()->getData())) {
            return $content->getContentVersion()->getData();
        }

        return [];
    }

    public function getNewContentVersion(ContentInterface $content, bool $linkVersion = true): ContentVersionInterface
    {
        $contentVersion = new ($this->configurationManager->getEntityClass('content_version'));
        $contentVersion->setData($content->getData());
        $contentVersion->setCreatedAt(new \DateTimeImmutable());
        $contentVersion->setUserId($this->userProvider->getUserId());

        $content->addVersion($contentVersion);
        if ($linkVersion) {
            $content->setContentVersion($contentVersion);
        }

        return $contentVersion;
    }

    public function getDraftContentVersion(ContentInterface $content): ContentVersionInterface
    {
        $userId = $this->userProvider->getUserId();
        $lastDraft = $this->getLastDraftVersionForUser($content->getVersions()->toArray(), $content->getContentVersion(), $userId);
        if (!$lastDraft instanceof VersionInterface || $lastDraft->getCreatedAt() < new \DateTimeImmutable('-1hour')) {
            $lastDraft = new ($this->configurationManager->getEntityClass('content_version'));
            $lastDraft->setContent($content);
            $lastDraft->setUserId($userId);
            $lastDraft->setAutoSave(true);
        }

        $lastDraft->setCreatedAt(new \DateTimeImmutable());

        return $lastDraft;
    }

    /**
     * @param array|VersionInterface[] $versions
     */
    private function getLastDraftVersionForUser(array $versions, ?VersionInterface $currentVersion, ?int $userId): ?VersionInterface
    {
        $currentVersionId = $currentVersion instanceof VersionInterface ? $currentVersion->getId() : null;
        $lastDraft = null;
        foreach ($versions as $version) {
            if ($currentVersionId === $version->getId()) {
                continue;
            }

            if ($version->getUserId() !== $userId) {
                continue;
            }

            if (!$version->isAutoSave()) {
                continue;
            }

            if (null === $lastDraft || $lastDraft->getCreatedAt() < $version->getCreatedAt()) {
                $lastDraft = $version;
            }
        }

        return $lastDraft;
    }

    public function getNewPageVersion(PageInterface $page): PageVersionInterface
    {
        $pageVersion = new ($this->configurationManager->getEntityClass('page_version'));
        $pageVersion->setCreatedAt(new \DateTimeImmutable());
        $pageVersion->setUserId($this->userProvider->getUserId());

        if (null !== $page->getContent()) {
            $contentVersion = $this->getNewContentVersion($page->getContent(), false);
            $pageVersion->setContentVersion($contentVersion);
        }

        if (null !== $page->getPageMeta()) {
            $pageMetaVersion = $this->getNewPageMetaVersion($page->getPageMeta());
            $pageVersion->setPageMetaVersion($pageMetaVersion);
        }

        $page->addVersion($pageVersion);
        $page->setPageVersion($pageVersion);

        return $pageVersion;
    }

    private function getNewPageMetaVersion(PageMetaInterface $pageMeta): PageMetaVersionInterface
    {
        $pageMetaVersion = new ($this->configurationManager->getEntityClass('page_meta_version'));
        $pageMetaVersion->setTitle($pageMeta->getTitle());
        $pageMetaVersion->setSlug($pageMeta->getSlug());
        $pageMetaVersion->setMetaTitle($pageMeta->getMetaTitle());
        $pageMetaVersion->setMetaDescription($pageMeta->getMetaDescription());
        $pageMetaVersion->setCreatedAt(new \DateTimeImmutable());
        $pageMetaVersion->setUserId($this->userProvider->getUserId());

        $pageMeta->addVersion($pageMetaVersion);

        return $pageMetaVersion;
    }

    public function getDraftPageVersion(PageInterface $page): PageVersionInterface
    {
        $userId = $this->userProvider->getUserId();
        /** @var PageVersionInterface $lastDraft */
        $lastDraft = $this->getLastDraftVersionForUser($page->getVersions()->toArray(), $page->getPageVersion(), $userId);
        if (null === $lastDraft || $lastDraft->getCreatedAt() < new \DateTimeImmutable('-1hour')) {
            $lastDraft = new ($this->configurationManager->getEntityClass('page_version'));
            $lastDraft->setPage($page);
            $lastDraft->setUserId($userId);
            $lastDraft->setAutoSave(true);
        }

        $lastDraft->setCreatedAt(new \DateTimeImmutable());

        if (null !== $page->getContent()) {
            $contentVersion = $lastDraft->getContentVersion();
            if (null === $contentVersion) {
                $contentVersion = $this->getNewContentVersion($page->getContent(), false);
                $contentVersion->setAutoSave(true);
                $lastDraft->setContentVersion($contentVersion);
            }

            $contentVersion->setCreatedAt(new \DateTimeImmutable());
            $contentVersion->setData($page->getContent()->getData());
        }

        if (null !== $page->getPageMeta()) {
            $pageMetaVersion = $lastDraft->getPageMetaVersion();
            if (null === $pageMetaVersion) {
                $pageMetaVersion = $this->getNewPageMetaVersion($page->getPageMeta());
                $pageMetaVersion->setAutoSave(true);
                $lastDraft->setPageMetaVersion($pageMetaVersion);
            }

            $pageMetaVersion->setCreatedAt(new \DateTimeImmutable());
            $pageMetaVersion->setTitle($page->getPageMeta()->getTitle());
            $pageMetaVersion->setSlug($page->getPageMeta()->getSlug());
            $pageMetaVersion->setMetaTitle($page->getPageMeta()->getMetaTitle());
            $pageMetaVersion->setMetaDescription($page->getPageMeta()->getMetaDescription());
        }

        return $lastDraft;
    }

    public function getPageVersionToLoad(PageInterface $page): ?PageVersionInterface
    {
        if (($mainRequest = $this->getRequest()) instanceof Request && $pageVersionId = $mainRequest->get('versionId')) {
            foreach ($page->getVersions() as $version) {
                if ($version->getId() === (int) $pageVersionId) {
                    return $version;
                }
            }
        }

        return $page->getPageVersion();
    }

    private function getRequest(): ?Request
    {
        if (method_exists($this->requestStack, 'getMainRequest')) {
            // SF >= 5.3
            return $this->requestStack->getMainRequest();
        }

        // compat SF < 5.3
        return $this->requestStack->getMasterRequest();
    }
}
