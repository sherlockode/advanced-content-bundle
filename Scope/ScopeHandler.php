<?php

namespace Sherlockode\AdvancedContentBundle\Scope;

use Doctrine\ORM\EntityManagerInterface;
use Sherlockode\AdvancedContentBundle\Manager\ConfigurationManager;
use Sherlockode\AdvancedContentBundle\Model\ContentInterface;
use Sherlockode\AdvancedContentBundle\Model\PageInterface;
use Sherlockode\AdvancedContentBundle\Model\ScopableInterface;

abstract class ScopeHandler implements ScopeHandlerInterface
{
    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * @var ConfigurationManager
     */
    protected $configurationManager;

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

        return $this->validateScopableEntity($content, $existingContents);
    }

    /**
     * @param PageInterface $page
     *
     * @return bool
     */
    public function isPageSlugValid(PageInterface $page): bool
    {
        $existingPages = $this->em->getRepository($this->configurationManager->getEntityClass('page'))->findAll();
        /** @var PageInterface $existingPage */
        foreach ($existingPages as $key => $existingPage) {
            if ($existingPage->getPageVersion() === null) {
                unset($existingPages[$key]);
                continue;
            }
            if ($existingPage->getPageVersion()->getPageMetaVersion() === null) {
                unset($existingPages[$key]);
                continue;
            }
            if ($existingPage->getPageVersion()->getPageMetaVersion()->getSlug() !== $page->getPageMeta()->getSlug()) {
                unset($existingPages[$key]);
                continue;
            }
        }
        $existingPages = array_values($existingPages);

        return $this->validateScopableEntity($page, $existingPages);
    }

    /**
     * @param PageInterface $page
     *
     * @return bool
     */
    public function isPageIdentifierValid(PageInterface $page): bool
    {
        $existingPages = $this->em->getRepository($this->configurationManager->getEntityClass('page'))->findBy([
            'pageIdentifier' => $page->getPageIdentifier(),
        ]);

        return $this->validateScopableEntity($page, $existingPages);
    }

    /**
     * @param ScopableInterface|ContentInterface|PageInterface             $scopable
     * @param array|ScopableInterface[]|ContentInterface[]|PageInterface[] $existingEntities
     *
     * @return bool
     */
    private function validateScopableEntity(ScopableInterface $scopable, array $existingEntities): bool
    {
        foreach ($existingEntities as $existingEntity) {
            if ($existingEntity->getId() === $scopable->getId()) {
                continue;
            }
            if (!$this->configurationManager->isScopesEnabled()) {
                return false;
            }
            $result = array_uintersect($scopable->getScopes()->toArray(), $existingEntity->getScopes()->toArray(), function ($a, $b) {
                return $a->getUnicityIdentifier() <=> $b->getUnicityIdentifier();
            });

            if (count($result) > 0) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param string $entityCode
     * @param array  $criteria
     *
     * @return ScopableInterface|null
     */
    public function getEntityForCurrentScope(string $entityCode, array $criteria): ?ScopableInterface
    {
        return $this->filterEntityForCurrentScope(
            $this->em->getRepository($this->configurationManager->getEntityClass($entityCode))->findBy($criteria)
        );
    }
}
