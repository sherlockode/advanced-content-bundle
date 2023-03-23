<?php

namespace Sherlockode\AdvancedContentBundle\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Sherlockode\AdvancedContentBundle\Form\Type\ElementsType;
use Sherlockode\AdvancedContentBundle\Form\Type\PageMetaType;
use Sherlockode\AdvancedContentBundle\Manager\ConfigurationManager;
use Sherlockode\AdvancedContentBundle\Manager\PageManager;
use Sherlockode\AdvancedContentBundle\Manager\VersionManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
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
     * @var VersionManager
     */
    private $versionManager;

    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @param EntityManagerInterface $em
     * @param ConfigurationManager   $configurationManager
     * @param PageManager            $pageManager
     * @param VersionManager         $versionManager
     * @param FormFactoryInterface   $formFactory
     */
    public function __construct(
        EntityManagerInterface $em,
        ConfigurationManager $configurationManager,
        PageManager $pageManager,
        VersionManager $versionManager,
        FormFactoryInterface $formFactory
    ) {
        $this->em = $em;
        $this->configurationManager = $configurationManager;
        $this->pageManager = $pageManager;
        $this->versionManager = $versionManager;
        $this->formFactory = $formFactory;
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

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function saveDraftAction(Request $request)
    {
        $id = $request->get('id');
        $page = $this->em->getRepository($this->configurationManager->getEntityClass('page'))->find($id);

        if ($page === null) {
            return new JsonResponse([
                'success' => false,
            ]);
        }

        $dataFormBuilder = $this->formFactory->createNamedBuilder('__field_name__', ElementsType::class, [], [
            'csrf_protection' => false,
        ]);
        $dataForm = $dataFormBuilder->getForm();
        // decode JSON data before the handleRequest call
        $data = $request->request->get('__field_name__');
        $request->request->set('__field_name__', json_decode($data, true));
        $dataForm->handleRequest($request);

        $metaFormBuilder = $this->formFactory->createNamedBuilder('__page_meta__', PageMetaType::class, $page->getPageMeta(), [
            'csrf_protection' => false,
        ]);
        $metaForm = $metaFormBuilder->getForm();
        $metaForm->handleRequest($request);

        if ($dataForm->isSubmitted() && $dataForm->isValid() && $metaForm->isSubmitted() && $metaForm->isValid()) {
            $page->getContent()->setData($dataForm->getData());
            $pageVersion = $this->versionManager->getDraftPageVersion($page);
            $this->em->persist($pageVersion);
            $this->em->flush();
            // reload content to retrieve new versions list
            $this->em->clear($this->configurationManager->getEntityClass('page'));
            $this->em->clear($this->configurationManager->getEntityClass('page_version'));
            $page = $this->em->getRepository($this->configurationManager->getEntityClass('page'))->find($id);

            return new JsonResponse([
                'success' => true,
                'html' => $this->renderView('@SherlockodeAdvancedContent/Version/page_list.html.twig', [
                    'page' => $page,
                ]),
            ]);
        }

        return new JsonResponse([
            'success' => false,
        ]);
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function deleteVersionAction(Request $request)
    {
        $id = $request->get('id');
        $page = $this->em->getRepository($this->configurationManager->getEntityClass('page'))->find($id);

        if ($page === null) {
            return new JsonResponse([
                'success' => false,
            ]);
        }

        $versionId = (int)$request->get('versionId');
        foreach ($page->getVersions() as $version) {
            if ($versionId === $version->getId()) {
                $this->em->remove($version);
                $this->em->flush();
                // reload content to retrieve new versions list
                $this->em->clear($this->configurationManager->getEntityClass('page'));
                $this->em->clear($this->configurationManager->getEntityClass('page_version'));
                $page = $this->em->getRepository($this->configurationManager->getEntityClass('page'))->find($id);

                return new JsonResponse([
                    'success' => true,
                    'html' => $this->renderView('@SherlockodeAdvancedContent/Version/page_list.html.twig', [
                        'page' => $page,
                    ]),
                ]);
            }
        }

        return new JsonResponse([
            'success' => false,
        ]);
    }
}
