<?php

namespace Sherlockode\AdvancedContentBundle\Controller;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityNotFoundException;
use Sherlockode\AdvancedContentBundle\Form\Type\ContentCreateType;
use Sherlockode\AdvancedContentBundle\Form\Type\ContentType;
use Sherlockode\AdvancedContentBundle\Form\Type\FlexibleGroupType;
use Sherlockode\AdvancedContentBundle\Manager\ConfigurationManager;
use Sherlockode\AdvancedContentBundle\Manager\ContentManager;
use Sherlockode\AdvancedContentBundle\Manager\ContentTypeManager;
use Sherlockode\AdvancedContentBundle\Model\FieldGroupValueInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class MyContentController
 */
class MyContentController extends Controller
{
    /**
     * @var ObjectManager
     */
    private $om;

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
     * @param ObjectManager        $om
     * @param ContentManager       $contentManager
     * @param ContentTypeManager   $contentTypeManager
     * @param ConfigurationManager $configurationManager
     */
    public function __construct(
        ObjectManager $om,
        ContentManager $contentManager,
        ContentTypeManager $contentTypeManager,
        ConfigurationManager $configurationManager
    ) {
        $this->om = $om;
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
            $this->om->flush();

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
            $this->om->persist($content);
            $this->om->flush();

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
            $this->om->persist($content);
            $this->om->flush();

            return $this->redirectToRoute('sherlockode_acb_content_edit', ['id' => $content->getId()]);
        }

        return $this->render('@SherlockodeAdvancedContent/Content/edit_content.html.twig', [
            'form' => $form->createView(),
            'is_ajax' => $request->isXmlHttpRequest(),
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

        $this->om->remove($content);
        $this->om->flush();

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

    /**
     * @return Response
     */
    public function flexibleFormAction(Request $request)
    {
        $contentTypeId = (int) $request->query->get('contentTypeId');
        $layoutId = (int) $request->query->get('layoutId');
        $parentFormId = $request->query->get('parentFormId');
        $contentTypeClass = $this->configurationManager->getEntityClass('content_type');
        $fieldGroupValueClass = $this->configurationManager->getEntityClass('field_group_value');
        $layoutClass = $this->configurationManager->getEntityClass('layout');
        $contentType = $this->getDoctrine()->getRepository($contentTypeClass)->find($contentTypeId);
        $layout = $this->getDoctrine()->getRepository($layoutClass)->find($layoutId);
        if (!$layout || !$contentType) {
            throw $this->createNotFoundException();
        }

        $name = '__flexible_name__';
        /** @var FieldGroupValueInterface $group */
        $group = new $fieldGroupValueClass();
        $group->setLayout($layout);

        $formBuilder = $this->get('form.factory')->createNamedBuilder($name, FlexibleGroupType::class, $group, [
            'contentType' => $contentType,
            'csrf_protection' => false,
            'parentFormId' => $parentFormId,
        ]);
        $form = $formBuilder->getForm();

        return $this->render('@SherlockodeAdvancedContent/Content/flexible_field_value.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
