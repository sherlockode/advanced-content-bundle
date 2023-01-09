<?php

namespace Sherlockode\AdvancedContentBundle\Controller\Crud;

use Doctrine\ORM\EntityManagerInterface;
use Sherlockode\AdvancedContentBundle\Form\Type\ContentType;
use Sherlockode\AdvancedContentBundle\Manager\ConfigurationManager;
use Sherlockode\AdvancedContentBundle\Manager\ContentManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ContentController
 */
class ContentController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var ContentManager
     */
    private $contentManager;

    /**
     * @var ConfigurationManager
     */
    private $configurationManager;

    /**
     * ContentController constructor.
     *
     * @param EntityManagerInterface $em
     * @param ContentManager         $contentManager
     * @param ConfigurationManager   $configurationManager
     */
    public function __construct(
        EntityManagerInterface $em,
        ContentManager $contentManager,
        ConfigurationManager $configurationManager
    ) {
        $this->em = $em;
        $this->contentManager = $contentManager;
        $this->configurationManager = $configurationManager;
    }

    /**
     * @param int     $id
     * @param Request $request
     *
     * @return Response
     */
    public function editAction($id, Request $request)
    {
        $content = $this->contentManager->getContentById($id);

        if ($content === null) {
            throw $this->createNotFoundException(
                sprintf('Entity %s with ID %s not found', $this->configurationManager->getEntityClass('content'), $id)
            );
        }

        $form = $this->createForm(ContentType::class, $content, [
            'action' => $this->generateUrl('sherlockode_acb_content_edit', ['id' => $content->getId()]),
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();

            return $this->redirectToRoute('sherlockode_acb_content_edit', ['id' => $content->getId()]);
        }

        return $this->render('@SherlockodeAdvancedContent/Content/edit_content.html.twig', [
                'form' => $form->createView(),
                'data' => $content,
            ]);
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function createAction(Request $request)
    {
        $contentEntityClass = $this->configurationManager->getEntityClass('content');
        $content = new $contentEntityClass;

        $form = $this->createForm(ContentType::class, $content, [
            'action' => $this->generateUrl('sherlockode_acb_content_create'),
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($content);
            $this->em->flush();

            return $this->redirectToRoute('sherlockode_acb_content_edit', ['id' => $content->getId()]);
        }

        return $this->render('@SherlockodeAdvancedContent/Content/create_content.html.twig', [
            'form' => $form->createView(),
            'data' => $content,
        ]);
    }

    /**
     * @return Response
     */
    public function listAction()
    {
        $contents = $this->contentManager->getContents();

        return $this->render('@SherlockodeAdvancedContent/Content/list.html.twig', [
            'contents' => $contents,
        ]);
    }

    /**
     * @param int $id
     *
     * @return Response
     */
    public function deleteAction($id)
    {
        $content = $this->contentManager->getContentById($id);

        if ($content === null) {
            throw $this->createNotFoundException(
                sprintf('Entity %s with ID %s not found', $this->configurationManager->getEntityClass('content'), $id)
            );
        }

        $this->em->remove($content);
        $this->em->flush();

        return $this->redirectToRoute('sherlockode_acb_content_list');
    }

    /**
     * @param int $id
     *
     * @return Response
     */
    public function showAction($id)
    {
        $content = $this->contentManager->getContentById($id);

        if ($content === null) {
            throw $this->createNotFoundException(
                sprintf('Entity %s with ID %s not found', $this->configurationManager->getEntityClass('content'), $id)
            );
        }

        return $this->render('@SherlockodeAdvancedContent/Content/show.html.twig', [
            'content' => $content,
        ]);
    }
}
