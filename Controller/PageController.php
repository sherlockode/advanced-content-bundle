<?php

namespace Sherlockode\AdvancedContentBundle\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;
use Sherlockode\AdvancedContentBundle\Form\Type\PageType;
use Sherlockode\AdvancedContentBundle\Manager\ConfigurationManager;
use Sherlockode\AdvancedContentBundle\Model\PageInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PageController extends AbstractController
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
     * @param int     $id
     * @param Request $request
     *
     * @return Response
     *
     * @throws EntityNotFoundException
     */
    public function editAction($id, Request $request)
    {
        $page = $this->em->getRepository($this->configurationManager->getEntityClass('page'))->find($id);

        if (!$page instanceof PageInterface) {
            throw EntityNotFoundException::fromClassNameAndIdentifier(
                $this->configurationManager->getEntityClass('page'),
                [$id]
            );
        }

        $form = $this->createForm(PageType::class, $page, [
            'action' => $this->generateUrl('sherlockode_acb_page_edit', ['id' => $page->getId()]),
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();

            return $this->redirectToRoute('sherlockode_acb_page_edit', ['id' => $page->getId()]);
        }

        return $this->render('@SherlockodeAdvancedContent/Page/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function createAction(Request $request)
    {
        $pageEntityClass = $this->configurationManager->getEntityClass('page');
        $page = new $pageEntityClass;

        $form = $this->createForm(PageType::class, $page, [
            'action' => $this->generateUrl('sherlockode_acb_page_create'),
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($page);
            $this->em->flush();

            return $this->redirectToRoute('sherlockode_acb_page_edit', ['id' => $page->getId()]);
        }

        return $this->render('@SherlockodeAdvancedContent/Page/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @return Response
     */
    public function listAction()
    {
        $pages = $this->em->getRepository($this->configurationManager->getEntityClass('page'))->findAll();

        return $this->render('@SherlockodeAdvancedContent/Page/list.html.twig', [
            'pages' => $pages,
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
        $page = $this->em->getRepository($this->configurationManager->getEntityClass('page'))->find($id);

        if (!$page instanceof PageInterface) {
            throw EntityNotFoundException::fromClassNameAndIdentifier(
                $this->configurationManager->getEntityClass('page'),
                [$id]
            );
        }

        $this->em->remove($page);
        $this->em->flush();

        return $this->redirectToRoute('sherlockode_acb_page_list');
    }
}
