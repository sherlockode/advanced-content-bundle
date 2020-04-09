<?php

namespace Sherlockode\AdvancedContentBundle\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;
use Sherlockode\AdvancedContentBundle\Form\Type\ContentCreateType;
use Sherlockode\AdvancedContentBundle\Form\Type\ContentType;
use Sherlockode\AdvancedContentBundle\Manager\ConfigurationManager;
use Sherlockode\AdvancedContentBundle\Manager\ContentManager;
use Sherlockode\AdvancedContentBundle\Manager\ContentTypeManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class MyContentController
 */
class MyContentController extends Controller
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
     * @var ContentTypeManager
     */
    private $contentTypeManager;

    /**
     * @var ConfigurationManager
     */
    private $configurationManager;

    /**
     * MyContentController constructor.
     *
     * @param EntityManagerInterface $em
     * @param ContentManager         $contentManager
     * @param ContentTypeManager     $contentTypeManager
     * @param ConfigurationManager   $configurationManager
     */
    public function __construct(
        EntityManagerInterface $em,
        ContentManager $contentManager,
        ContentTypeManager $contentTypeManager,
        ConfigurationManager $configurationManager
    ) {
        $this->em = $em;
        $this->contentManager = $contentManager;
        $this->contentTypeManager = $contentTypeManager;
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
        $content = $this->contentManager->getContentById($id);

        if ($content === null) {
            throw EntityNotFoundException::fromClassNameAndIdentifier(
                $this->configurationManager->getEntityClass('content'),
                [$id]
            );
        }

        $form = $this->createForm(ContentType::class, $content, [
            'action' => $this->generateUrl('sherlockode_acb_content_edit', ['id' => $content->getId()]),
            'contentType' => $content->getContentType(),
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
     * @param Request              $request
     *
     * @return Response
     */
    public function createAction(Request $request)
    {
        $contentEntityClass = $this->configurationManager->getEntityClass('content');
        $content = new $contentEntityClass;

        $form = $this->createForm(ContentCreateType::class, $content, [
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
     * @param int                  $id
     * @param Request              $request
     *
     * @return Response
     *
     * @throws EntityNotFoundException
     */
    public function createByTypeAction($id, Request $request)
    {
        $contentType = $this->contentTypeManager->getContentTypeById($id);

        if ($contentType === null) {
            throw EntityNotFoundException::fromClassNameAndIdentifier(
                $this->configurationManager->getEntityClass('content_type'),
                [$id]
            );
        }

        $contentEntityClass = $this->configurationManager->getEntityClass('content');
        $content = new $contentEntityClass;
        $content->setContentType($contentType);

        $form = $this->createForm(ContentType::class, $content, [
            'action' => $this->generateUrl('sherlockode_acb_content_create_by_type', ['id' => $contentType->getId()]),
            'contentType' => $content->getContentType(),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($content);
            $this->em->flush();

            return $this->redirectToRoute('sherlockode_acb_content_edit', ['id' => $content->getId()]);
        }

        return $this->render('@SherlockodeAdvancedContent/Content/_form.html.twig', [
            'form' => $form->createView(),
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
     *
     * @throws EntityNotFoundException
     */
    public function deleteAction($id)
    {
        $content = $this->contentManager->getContentById($id);

        if ($content === null) {
            throw EntityNotFoundException::fromClassNameAndIdentifier(
                $this->configurationManager->getEntityClass('content'),
                [$id]
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
     *
     * @throws EntityNotFoundException
     */
    public function showAction($id)
    {
        $content = $this->contentManager->getContentById($id);

        if ($content === null) {
            throw EntityNotFoundException::fromClassNameAndIdentifier(
                $this->configurationManager->getEntityClass('content'),
                [$id]
            );
        }

        return $this->render('@SherlockodeAdvancedContent/Content/show.html.twig', [
            'content' => $content,
        ]);
    }
}
