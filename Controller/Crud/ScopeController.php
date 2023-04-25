<?php

namespace Sherlockode\AdvancedContentBundle\Controller\Crud;

use Doctrine\ORM\EntityManagerInterface;
use Sherlockode\AdvancedContentBundle\Manager\ConfigurationManager;
use Sherlockode\AdvancedContentBundle\Model\ScopeInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ScopeController extends AbstractController
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
    public function __construct(
        EntityManagerInterface $em,
        ConfigurationManager $configurationManager
    ) {
        $this->em = $em;
        $this->configurationManager = $configurationManager;
    }

    /**
     * @param int $id
     *
     * @return Response
     */
    public function deleteAction($id)
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
