<?php

namespace Sherlockode\AdvancedContentBundle\Manager;

use Sherlockode\AdvancedContentBundle\Model\ContentInterface;
use Sherlockode\AdvancedContentBundle\Model\ContentVersionInterface;
use Sherlockode\AdvancedContentBundle\Model\PageInterface;
use Sherlockode\AdvancedContentBundle\Model\PageMetaInterface;
use Sherlockode\AdvancedContentBundle\Model\PageMetaVersionInterface;
use Sherlockode\AdvancedContentBundle\Model\PageVersionInterface;
use Sherlockode\AdvancedContentBundle\User\UserProviderInterface;
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

    /**
     * @param ConfigurationManager  $configurationManager
     * @param UserProviderInterface $userProvider
     * @param RequestStack          $requestStack
     */
    public function __construct(
        ConfigurationManager $configurationManager,
        UserProviderInterface $userProvider,
        RequestStack $requestStack
    ) {
        $this->configurationManager = $configurationManager;
        $this->userProvider = $userProvider;
        $this->requestStack = $requestStack;
    }

    /**
     * @param ContentInterface $content
     *
     * @return array
     */
    public function getContentData(ContentInterface $content): array
    {
        if ($mainRequest = $this->requestStack->getMainRequest()) {
            if ($contentVersionId = $mainRequest->get('versionId')) {
                foreach ($content->getVersions() as $version) {
                    if ($version->getId() === (int)$contentVersionId) {
                        return $version->getData();
                    }
                }
            }
        }

        if ($content->getContentVersion() !== null && !empty($content->getContentVersion()->getData())) {
            return $content->getContentVersion()->getData();
        }

        return [];
    }

    /**
     * @param ContentInterface $content
     * @param bool             $linkVersion
     *
     * @return ContentVersionInterface
     */
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

    /**
     * @param ContentInterface $content
     *
     * @return ContentVersionInterface
     */
    public function getDraftContentVersion(ContentInterface $content): ContentVersionInterface
    {
        $userId = $this->userProvider->getUserId();
        $lastDraft = $this->getLastDraftVersionForUser($content, $userId);
        if ($lastDraft === null || $lastDraft->getCreatedAt() < new \DateTimeImmutable('-1hour')) {
            $lastDraft = new ($this->configurationManager->getEntityClass('content_version'));
            $lastDraft->setContent($content);
            $lastDraft->setUserId($userId);
            $lastDraft->setAutoSave(true);
        }
        $lastDraft->setCreatedAt(new \DateTimeImmutable());

        return $lastDraft;
    }

    /**
     * @param ContentInterface $content
     * @param int|null         $userId
     *
     * @return ContentVersionInterface|null
     */
    private function getLastDraftVersionForUser(ContentInterface $content, ?int $userId): ?ContentVersionInterface
    {
        $currentContentVersionId = $content->getContentVersion() === null ? null : $content->getContentVersion()->getId();
        $lastDraft = null;
        foreach ($content->getVersions() as $version) {
            if ($currentContentVersionId === $version->getId()) {
                continue;
            }
            if ($version->getUserId() !== $userId) {
                continue;
            }
            if (!$version->isAutoSave()) {
                continue;
            }
            if ($lastDraft === null || $lastDraft->getCreatedAt() < $version->getCreatedAt()) {
                $lastDraft = $version;
            }
        }

        return $lastDraft;
    }

    /**
     * @param PageInterface $page
     *
     * @return PageVersionInterface
     */
    public function getNewPageVersion(PageInterface $page): PageVersionInterface
    {
        $pageVersion = new ($this->configurationManager->getEntityClass('page_version'));
        $pageVersion->setCreatedAt(new \DateTimeImmutable());
        $pageVersion->setUserId($this->userProvider->getUserId());

        if ($page->getContent() !== null) {
            $contentVersion = $this->getNewContentVersion($page->getContent(), false);
            $pageVersion->setContentVersion($contentVersion);
        }
        if ($page->getPageMeta() !== null) {
            $pageMetaVersion = $this->getNewPageMetaVersion($page->getPageMeta());
            $pageVersion->setPageMetaVersion($pageMetaVersion);
        }

        $page->addVersion($pageVersion);
        $page->setPageVersion($pageVersion);

        return $pageVersion;
    }

    /**
     * @param PageMetaInterface $pageMeta
     *
     * @return PageMetaVersionInterface
     */
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

    /**
     * @param PageInterface $page
     *
     * @return PageVersionInterface|null
     */
    public function getPageVersionToLoad(PageInterface $page): ?PageVersionInterface
    {
        if ($page->getPageVersion() !== null) {
            return $page->getPageVersion();
        }

        return null;
    }
}
