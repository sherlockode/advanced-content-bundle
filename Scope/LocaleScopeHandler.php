<?php

namespace Sherlockode\AdvancedContentBundle\Scope;

use Doctrine\ORM\EntityManagerInterface;
use Sherlockode\AdvancedContentBundle\Manager\ConfigurationManager;
use Sherlockode\AdvancedContentBundle\Model\ScopableInterface;
use Sherlockode\AdvancedContentBundle\Model\ScopeInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class LocaleScopeHandler extends ScopeHandler
{
    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @param EntityManagerInterface $em
     * @param ConfigurationManager   $configurationManager
     * @param RequestStack           $requestStack
     */
    public function __construct(
        EntityManagerInterface $em,
        ConfigurationManager $configurationManager,
        RequestStack $requestStack
    ) {
        parent::__construct($em, $configurationManager);

        $this->requestStack = $requestStack;
    }

    /**
     * @return string|null
     */
    public function getScopeGroupBy(): ?string
    {
        return null;
    }

    /**
     * @param array $data
     *
     * @return ScopeInterface|null
     */
    public function getScopeFromData(array $data): ?ScopeInterface
    {
        if (empty($data['locale'])) {
            return null;
        }

        return $this->em->getRepository($this->configurationManager->getEntityClass('scope'))->findOneBy([
            'locale' => $data['locale'],
        ]);
    }

    /**
     * @param ScopeInterface $scope
     *
     * @return array
     */
    public function getDataFromScope(ScopeInterface $scope): array
    {
        return [
            'locale' => $scope->getLocale(),
        ];
    }

    /**
     * @return ScopeInterface|null
     */
    public function getCurrentScope(): ?ScopeInterface
    {
        if (!$this->configurationManager->isScopesEnabled()) {
            return null;
        }

        if (method_exists($this->requestStack, 'getMainRequest')) {
            // SF >= 5.3
            $mainRequest = $this->requestStack->getMainRequest();
        } else {
            // compat SF < 5.3
            $mainRequest = $this->requestStack->getMasterRequest();
        }
        if ($mainRequest === null) {
            return null;
        }

        if (!$mainRequest->getLocale()) {
            return null;
        }

        return $this->em->getRepository($this->configurationManager->getEntityClass('scope'))->findOneBy([
            'locale' => $mainRequest->getLocale(),
        ]);
    }
}
