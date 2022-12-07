<?php

namespace Sherlockode\AdvancedContentBundle\Controller;

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
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var ConfigurationManager
     */
    private $configurationManager;

    /**
     * @var PageManager
     */
    private $pageManager;

    /**
     * @param EntityManagerInterface $em
     * @param ConfigurationManager   $configurationManager
     * @param PageManager            $pageManager
     */
    public function __construct(
        EntityManagerInterface $em,
        ConfigurationManager $configurationManager,
        PageManager $pageManager
    ) {
        $this->em = $em;
        $this->configurationManager = $configurationManager;
        $this->pageManager = $pageManager;
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function duplicateAction(Request $request)
    {
        $id = $request->get('id');
        $page = $this->em->getRepository($this->configurationManager->getEntityClass('page'))->find($id);

        if ($page === null) {
            throw $this->createNotFoundException(
                sprintf('Entity %s with ID %s not found', $this->configurationManager->getEntityClass('page'), $id)
            );
        }

        $newPage = $this->pageManager->duplicate($page);
        $this->em->persist($newPage);
        $this->em->flush();

        return new RedirectResponse($request->server->get('HTTP_REFERER', '/'));
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function duplicateLocaleAction(Request $request)
    {
        $id = $request->get('id');
        $pageMeta = $this->pageManager->getPageMetaById($id);

        if ($pageMeta === null) {
            throw $this->createNotFoundException(
                sprintf('Entity %s with ID %s not found', $this->configurationManager->getEntityClass('page_meta'), $id)
            );
        }

        $newPageMeta = clone $pageMeta;
        $newPageMeta->setLocale($request->get('locale'));

        $this->em->persist($newPageMeta);
        $this->em->flush();

        return new RedirectResponse($request->server->get('HTTP_REFERER', '/'));
    }

    /**
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function deleteLocaleAction(Request $request)
    {
        $id = $request->get('id');
        $pageMeta = $this->pageManager->getPageMetaById($id);

        if ($pageMeta === null) {
            throw $this->createNotFoundException(
                sprintf('Entity %s with ID %s not found', $this->configurationManager->getEntityClass('page_meta'), $id)
            );
        }

        $this->em->remove($pageMeta);
        $this->em->flush();

        return new RedirectResponse($request->server->get('HTTP_REFERER', '/'));
    }
}
