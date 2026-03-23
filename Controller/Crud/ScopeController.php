<?php

declare(strict_types=1);

namespace Sherlockode\AdvancedContentBundle\Controller\Crud;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Doctrine\ORM\EntityManagerInterface;
use Sherlockode\AdvancedContentBundle\Manager\ConfigurationManager;
use Sherlockode\AdvancedContentBundle\Model\ScopeInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class ScopeController extends AbstractController
{
    public function __construct(private readonly EntityManagerInterface $em, private readonly ConfigurationManager $configurationManager)
    {
    }

    /**
     * @param int $id
     *
     * @return Response
     */
    public function delete($id): RedirectResponse
    {
        $scope = $this->em->getRepository($this->configurationManager->getEntityClass('scope'))->find($id);

        if (!$scope instanceof ScopeInterface) {
            throw $this->createNotFoundException(
                sprintf('Entity %s with ID %s not found', $this->configurationManager->getEntityClass('scope'), $id)
            );
        }

        $this->em->remove($scope);
        $this->em->flush();

        return $this->redirectToRoute('sherlockode_acb_tools_index');
    }
}
