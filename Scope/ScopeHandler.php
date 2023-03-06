<?php

namespace Sherlockode\AdvancedContentBundle\Scope;

use Doctrine\ORM\EntityManagerInterface;
use Sherlockode\AdvancedContentBundle\Manager\ConfigurationManager;
use Sherlockode\AdvancedContentBundle\Model\ContentInterface;
use Sherlockode\AdvancedContentBundle\Model\ScopableInterface;

abstract class ScopeHandler implements ScopeHandlerInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var ConfigurationManager
     */
    private $configurationManager;

    /**
     * @param EntityManagerInterface $em
     * @param ConfigurationManager   $configurationManager
     */
    public function __construct(EntityManagerInterface $em, ConfigurationManager $configurationManager)
    {
        $this->em = $em;
        $this->configurationManager = $configurationManager;
    }

    /**
     * @param ContentInterface $content
     *
     * @return bool
     */
    public function isContentSlugValid(ContentInterface $content): bool
    {
        $existingContents = $this->em->getRepository($this->configurationManager->getEntityClass('content'))->findBy([
            'slug' => $content->getSlug(),
        ]);
        /** @var ContentInterface|ScopableInterface $existingContent */
        foreach ($existingContents as $existingContent) {
            if ($existingContent->getId() === $content->getId()) {
                continue;
            }
            if (!$this->configurationManager->isScopesEnabled()) {
                return false;
            }
            $result = array_uintersect($content->getScopes()->toArray(), $existingContent->getScopes()->toArray(), function ($a, $b) {
                return $a->getUnicityIdentifier() <=> $b->getUnicityIdentifier();
            });

            if (count($result) > 0) {
                return false;
            }
        }

        return true;
    }
}
