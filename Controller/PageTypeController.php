<?php

namespace Sherlockode\AdvancedContentBundle\Controller;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityNotFoundException;
use Sherlockode\AdvancedContentBundle\Form\Type\PageTypeType;
use Sherlockode\AdvancedContentBundle\Manager\ConfigurationManager;
use Sherlockode\AdvancedContentBundle\Model\PageTypeInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PageTypeController extends AbstractController
{
    /**
     * @var ObjectManager
     */
    private $om;

    /**
     * @var ConfigurationManager
     */
    private $configurationManager;

    /**
     * @param ObjectManager        $om
     * @param ConfigurationManager $configurationManager
     */
    public function __construct(
        ObjectManager $om,
        ConfigurationManager $configurationManager
    ) {
        $this->om = $om;
        $this->configurationManager = $configurationManager;
    }

    /**
     * @param int                  $id
     * @param Request              $request
     *
     * @return Response
     *
     * @throws EntityNotFoundException
     */
    public function editAction($id, Request $request)
    {
        $pageType = $this->om->getRepository($this->configurationManager->getEntityClass('page_type'))->find($id);

        if (!$pageType instanceof PageTypeInterface) {
            throw EntityNotFoundException::fromClassNameAndIdentifier(
                $this->configurationManager->getEntityClass('page_type'),
                [$id]
            );
        }

        $form = $this->createForm(PageTypeType::class, $pageType, [
            'action' => $this->generateUrl('sherlockode_acb_page_type_edit', ['id' => $pageType->getId()]),
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->om->flush();

            return $this->redirectToRoute('sherlockode_acb_page_type_edit', ['id' => $pageType->getId()]);
        }

        return $this->render('@SherlockodeAdvancedContent/PageType/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param Request              $request
     *
     * @return Response
     */
    public function createAction(Request $request)
    {
        $pageTypeEntityClass = $this->configurationManager->getEntityClass('page_type');
        $pageType = new $pageTypeEntityClass;

        $form = $this->createForm(PageTypeType::class, $pageType, [
            'action' => $this->generateUrl('sherlockode_acb_page_type_create'),
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->om->persist($pageType);
            $this->om->flush();

            return $this->redirectToRoute('sherlockode_acb_page_type_edit', ['id' => $pageType->getId()]);
        }

        return $this->render('@SherlockodeAdvancedContent/PageType/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @return Response
     */
    public function listAction()
    {
        $pageTypes = $this->om->getRepository($this->configurationManager->getEntityClass('page_type'))->findAll();

        return $this->render('@SherlockodeAdvancedContent/PageType/list.html.twig', [
            'pageTypes' => $pageTypes,
        ]);
    }

    /**
     * @param int $id
     *
     * @return Response
     *
     * @throws EntityNotFoundException
     */
    public function deleteAction($id)
    {
        $pageType = $this->om->getRepository($this->configurationManager->getEntityClass('page_type'))->find($id);

        if (!$pageType instanceof PageTypeInterface) {
            throw EntityNotFoundException::fromClassNameAndIdentifier(
                $this->configurationManager->getEntityClass('page_type'),
                [$id]
            );
        }

        $this->om->remove($pageType);
        $this->om->flush();

        return $this->redirectToRoute('sherlockode_acb_page_type_list');
    }
}
