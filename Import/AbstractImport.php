<?php

namespace Sherlockode\AdvancedContentBundle\Import;

use Cocur\Slugify\Slugify;
use Doctrine\ORM\EntityManagerInterface;
use Sherlockode\AdvancedContentBundle\Manager\ConfigurationManager;
use Sherlockode\AdvancedContentBundle\Model\ScopableInterface;
use Sherlockode\AdvancedContentBundle\Model\ScopeInterface;
use Sherlockode\AdvancedContentBundle\Scope\ScopeHandlerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

abstract class AbstractImport
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
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @var ScopeHandlerInterface
     */
    protected $scopeHandler;

    /**
     * @var array
     */
    protected $entityClasses = [];

    /**
     * @var bool
     */
    protected $allowUpdate = true;

    /**
     * @var Slugify
     */
    protected $slugify;

    /**
     * @var array
     */
    protected $errors = [];

    public function __construct(
        EntityManagerInterface $em,
        ConfigurationManager $configurationManager,
        TranslatorInterface $translator,
        ScopeHandlerInterface $scopeHandler,
    ) {
        $this->em = $em;
        $this->configurationManager = $configurationManager;
        $this->translator = $translator;
        $this->scopeHandler = $scopeHandler;
        $this->init();
    }

    protected function init()
    {
        $this->entityClasses = $this->configurationManager->getEntityClasses();
        $this->slugify = new Slugify();
    }

    /**
     * @param bool $allowUpdate
     *
     * @return $this
     */
    public function setAllowUpdate($allowUpdate)
    {
        $this->allowUpdate = $allowUpdate;

        return $this;
    }

    /**
     * @param string $slug
     * @param array  $data
     *
     * @return ImportResult
     */
    public function importData($slug, $data)
    {
        $result = new ImportResult();
        $result->success();

        try {
            $this->resetErrors();

            $this->importEntity($slug, $data);
            foreach ($this->errors as $error) {
                $result->addMessage($error);
            }

            if (count($this->errors) > 0) {
                $result->failure();
            }
        } catch (\Exception $exception) {
            $result
                ->failure()
                ->addMessage($exception->getMessage())
            ;
        }

        return $result;
    }

    /**
     * @return $this
     */
    public function resetErrors()
    {
        $this->errors = [];

        return $this;
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * @throws \Exception
     */
    protected function getScopesForEntity(array $scopesData): array
    {
        if (!$this->configurationManager->isScopesEnabled()) {
            if ([] !== $scopesData) {
                throw new \Exception($this->translator->trans('init.errors.scopes_disabled', [], 'AdvancedContentBundle'));
            }

            return [];
        }

        $scopes = [];
        foreach ($scopesData as $scopeData) {
            $scope = $this->scopeHandler->getScopeFromData($scopeData);
            if (null === $scope) {
                throw new \Exception($this->translator->trans('init.errors.unknown_scope', ['%scope%' => json_encode($scopeData)], 'AdvancedContentBundle'));
            }

            $scopes[] = $scope;
        }

        return $scopes;
    }

    /**
     * @throws \Exception
     */
    protected function getExistingScopableEntity(string $entityClass, array $criteria, array $scopes): ?ScopableInterface
    {
        $existingEntities = $this->em->getRepository($entityClass)->findBy($criteria);
        if (0 === count($existingEntities)) {
            return null;
        }

        if (1 === count($existingEntities)) {
            return reset($existingEntities);
        }

        $entity = null;
        foreach ($existingEntities as $existingEntity) {
            $result = array_uintersect($scopes, $existingEntity->getScopes()->toArray(), fn ($a, $b) => $a->getUnicityIdentifier() <=> $b->getUnicityIdentifier());

            if (count($result) === count($scopes)) {
                return $existingEntity;
            }

            if ([] !== $result) {
                if (null !== $entity) {
                    throw new \Exception($this->translator->trans('init.errors.multiple_entities_same_scope', [], 'AdvancedContentBundle'));
                }

                $entity = $existingEntity;
            }
        }

        return $entity;
    }

    /**
     * @param array|ScopeInterface[] $scopes
     */
    protected function updateEntityScopes(ScopableInterface $entity, array $scopes): void
    {
        foreach ($entity->getScopes() as $existingScope) {
            foreach ($scopes as $key => $scope) {
                if ($scope->getUnicityIdentifier() === $existingScope->getUnicityIdentifier()) {
                    unset($scopes[$key]);
                    continue 2;
                }
            }

            $entity->removeScope($existingScope);
        }

        foreach ($scopes as $scope) {
            $entity->addScope($scope);
        }
    }

    /**
     * @param string $slug
     * @param array  $data
     *
     * @return void
     */
    abstract protected function importEntity($slug, $data);
}
