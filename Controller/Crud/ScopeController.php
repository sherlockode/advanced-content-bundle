<?php

namespace Sherlockode\AdvancedContentBundle\Controller\Crud;

use Doctrine\ORM\EntityManagerInterface;
use Sherlockode\AdvancedContentBundle\Form\Type\ScopeType;
use Sherlockode\AdvancedContentBundle\Manager\ConfigurationManager;
use Sherlockode\AdvancedContentBundle\Model\ScopeInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

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
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @param EntityManagerInterface $em
     * @param ConfigurationManager   $configurationManager
     * @param TranslatorInterface    $translator
     */
    public function __construct(
        EntityManagerInterface $em,
        ConfigurationManager $configurationManager,
        TranslatorInterface $translator
    ) {
        $this->em = $em;
        $this->configurationManager = $configurationManager;
        $this->translator = $translator;
    }

    /**
     * @return Response
     */
    public function listAction(Request $request)
    {
        $scopes = $this->em->getRepository($this->configurationManager->getEntityClass('scope'))->findAll();

        $scopeEntityClass = $this->configurationManager->getEntityClass('scope');
        $scope = new $scopeEntityClass;

        $form = $this->createForm(ScopeType::class, $scope, [
            'action' => $this->generateUrl('sherlockode_acb_scope_list'),
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $existingScopes = $this->em->getRepository($this->configurationManager->getEntityClass('scope'))->findBy([
                'locale' => $scope->getLocale(),
            ]);
            if ($existingScopes === 0) {
                $this->em->persist($scope);
                $this->em->flush();

                return $this->redirectToRoute('sherlockode_acb_scope_list');
            } else {
                $form->addError(new FormError(
                    $this->translator->trans('scope.errors.unique_locale', [], 'AdvancedContentBundle')
                ));
            }
        }

        return $this->render('@SherlockodeAdvancedContent/Scope/list.html.twig', [
            'scopes' => $scopes,
            'form' => $form->createView(),
        ]);
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

        return $this->redirectToRoute('sherlockode_acb_scope_list');
    }
}
