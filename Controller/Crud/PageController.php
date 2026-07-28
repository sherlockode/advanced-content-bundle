<?php

declare(strict_types=1);

namespace Sherlockode\AdvancedContentBundle\Controller\Crud;

use Doctrine\ORM\EntityManagerInterface;
use Sherlockode\AdvancedContentBundle\Form\Type\PageType;
use Sherlockode\AdvancedContentBundle\Manager\ConfigurationManager;
use Sherlockode\AdvancedContentBundle\Manager\PageManager;
use Sherlockode\AdvancedContentBundle\Model\PageInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PageController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly ConfigurationManager $configurationManager,
        private readonly PageManager $pageManager,
    ) {
    }

    /**
     * @param int $id
     *
     * @return Response
     */
    public function edit($id, Request $request): RedirectResponse|Response
    {
        $page = $this->em->getRepository($this->configurationManager->getEntityClass('page'))->find($id);

        if (!$page instanceof PageInterface) {
            throw $this->createNotFoundException(sprintf('Entity %s with ID %s not found', $this->configurationManager->getEntityClass('page'), $id));
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
     * @return Response
     */
    public function create(Request $request): RedirectResponse|Response
    {
        if ($id = $request->get('duplicateId')) {
            $pageToDuplicate = $this->em->getRepository($this->configurationManager->getEntityClass('page'))->find($id);
            if (!$pageToDuplicate instanceof PageInterface) {
                throw $this->createNotFoundException(sprintf('Entity %s with ID %s not found', $this->configurationManager->getEntityClass('page'), $id));
            }

            $page = $this->pageManager->duplicate($pageToDuplicate);
        } else {
            $pageEntityClass = $this->configurationManager->getEntityClass('page');
            $page = new $pageEntityClass();
        }

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

    public function list(): Response
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
     */
    public function delete($id): RedirectResponse
    {
        $page = $this->em->getRepository($this->configurationManager->getEntityClass('page'))->find($id);

        if (!$page instanceof PageInterface) {
            throw $this->createNotFoundException(sprintf('Entity %s with ID %s not found', $this->configurationManager->getEntityClass('page'), $id));
        }

        $this->em->remove($page);
        $this->em->flush();

        return $this->redirectToRoute('sherlockode_acb_page_list');
    }
}
