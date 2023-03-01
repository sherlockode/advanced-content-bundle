<?php

namespace Sherlockode\AdvancedContentBundle\Manager;

use Sherlockode\AdvancedContentBundle\Model\ContentInterface;
use Sherlockode\AdvancedContentBundle\Model\ContentVersionInterface;
use Sherlockode\AdvancedContentBundle\User\UserProviderInterface;

class ContentVersionManager
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
     * @param ConfigurationManager  $configurationManager
     * @param UserProviderInterface $userProvider
     */
    public function __construct(ConfigurationManager $configurationManager, UserProviderInterface $userProvider)
    {
        $this->configurationManager = $configurationManager;
        $this->userProvider = $userProvider;
    }

    /**
     * @param ContentInterface $content
     *
     * @return array
     */
    public function getContentData(ContentInterface $content): array
    {
        if ($content->getContentVersion() !== null && !empty($content->getContentVersion()->getData())) {
            return $content->getContentVersion()->getData();
        }

        $emptyRowCol = [
            'elementType' => 'row',
            'position' => 0,
            'elements' => [
                [
                    'elementType' => 'column',
                    'position' => 0,
                    'config' => [
                        'size' => 12,
                    ],
                    'elements' => [],
                ],
            ],
        ];

        return [$emptyRowCol];
    }

    /**
     * @param ContentInterface $content
     *
     * @return ContentVersionInterface
     */
    public function getNewContentVersion(ContentInterface $content): ?ContentVersionInterface
    {
        $contentVersion = new ($this->configurationManager->getEntityClass('content_version'));
        $contentVersion->setData($content->getData());
        $contentVersion->setCreatedAt(new \DateTimeImmutable());
        $contentVersion->setUserId($this->userProvider->getUserId());
        $content->addVersion($contentVersion);
        $content->setContentVersion($contentVersion);

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
            if ($lastDraft === null || $lastDraft->getCreatedAt() < $version->getCreatedAt()) {
                $lastDraft = $version;
            }
        }

        return $lastDraft;
    }
}
