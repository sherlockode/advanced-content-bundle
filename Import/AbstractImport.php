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

    /**
     * @param EntityManagerInterface $em
     * @param ConfigurationManager   $configurationManager
     * @param TranslatorInterface    $translator
     * @param ScopeHandlerInterface  $scopeHandler
     */
    public function __construct(
        EntityManagerInterface $em,
        ConfigurationManager $configurationManager,
        TranslatorInterface $translator,
        ScopeHandlerInterface $scopeHandler
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
        } catch (\Exception $e) {
            $result
                ->failure()
                ->addMessage($e->getMessage())
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
     * @param array $scopesData
     *
     * @return array
     *
     * @throws \Exception
     */
    protected function getScopesForEntity(array $scopesData): array
    {
        if (!$this->configurationManager->isScopesEnabled()) {
            if (count($scopesData) > 0) {
                throw new \Exception($this->translator->trans(
                    'init.errors.scopes_disabled',
                    [],
                    'AdvancedContentBundle'
                ));
            }
            return [];
        }

        $scopes = [];
        foreach ($scopesData as $scopeData) {
            $scope = $this->scopeHandler->getScopeFromData($scopeData);
            if ($scope === null) {
                throw new \Exception($this->translator->trans(
                    'init.errors.unknown_scope',
                    ['%scope%' => json_encode($scopeData)],
                    'AdvancedContentBundle'
                ));
            }
            $scopes[] = $scope;
        }

        return $scopes;
    }

    /**
     * @param string $entityClass
     * @param array  $criteria
     * @param array  $scopes
     *
     * @return ScopableInterface|null
     *
     * @throws \Exception
     */
    protected function getExistingScopableEntity(string $entityClass, array $criteria, array $scopes): ?ScopableInterface
    {
        $existingEntities = $this->em->getRepository($entityClass)->findBy($criteria);
        if (count($existingEntities) === 0) {
            return null;
        }
        if (count($existingEntities) === 1) {
            return reset($existingEntities);
        }

        $entity = null;
        foreach ($existingEntities as $existingEntity) {
            $result = array_uintersect($scopes, $existingEntity->getScopes()->toArray(), function ($a, $b) {
                return $a->getUnicityIdentifier() <=> $b->getUnicityIdentifier();
            });

            if (count($result) === count($scopes)) {
                return $existingEntity;
            }

            if (count($result) > 0) {
                if ($entity !== null) {
                    throw new \Exception($this->translator->trans(
                        'init.errors.multiple_entities_same_scope',
                        [],
                        'AdvancedContentBundle'
                    ));
                }
                $entity = $existingEntity;
            }
        }

        return $entity;
    }

    /**
     * @param ScopableInterface      $entity
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
