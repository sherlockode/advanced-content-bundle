<?php

namespace Sherlockode\AdvancedContentBundle\Controller;

use Doctrine\ORM\EntityNotFoundException;
use Sherlockode\AdvancedContentBundle\Manager\ConfigurationManager;
use Sherlockode\AdvancedContentBundle\Manager\ContentManager;
use Sherlockode\AdvancedContentBundle\Manager\ContentTypeManager;
use Sherlockode\AdvancedContentBundle\Manager\FormBuilderManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class MyContentController
 *
 * @Route("/mycontent")
 */
class MyContentController extends Controller
{
    /**
     * @Route("/{id}/update", name="sherlockode_ac_edit_mycontent")
     *
     * @param int                  $id
     * @param Request              $request
     * @param ObjectManager        $om
     * @param FormBuilderManager   $formBuilderManager
     * @param ContentManager       $contentManager
     * @param ConfigurationManager $configurationManager
     *
     * @return Response
     *
     * @throws EntityNotFoundException
     */
    public function editAction(
        $id,
        Request $request,
        ObjectManager $om,
        FormBuilderManager $formBuilderManager,
        ContentManager $contentManager,
        ConfigurationManager $configurationManager
    ) {
        $content = $contentManager->getContentById($id);

        if ($content === null) {
            throw EntityNotFoundException::fromClassNameAndIdentifier(
                $configurationManager->getEntityClass('content'),
                [$id]
            );
        }

        $formBuilder = $this->createFormBuilder($content, [
            'action' => $this->generateUrl('sherlockode_ac_edit_mycontent', ['id' => $content->getId()])
        ]);

        $formBuilderManager->buildContentForm($formBuilder, $content);

        $form = $formBuilder->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $om->flush();

            return $this->redirectToRoute('sherlockode_ac_edit_mycontent', ['id' => $content->getId()]);
        }

        return $this->render('SherlockodeAdvancedContentBundle:Content:edit_content.html.twig', [
                'form' => $form->createView(),
                'data' => $content,
            ]);
    }

    /**
     * @Route("/create", name="sherlockode_ac_create_mycontent")
     *
     * @param Request              $request
     * @param ObjectManager        $om
     * @param FormBuilderManager   $formBuilderManager
     * @param ConfigurationManager $configurationManager
     *
     * @return Response
     */
    public function createAction(
        Request $request,
        ObjectManager $om,
        FormBuilderManager $formBuilderManager,
        ConfigurationManager $configurationManager
    ) {
        $contentEntityClass = $configurationManager->getEntityClass('content');
        $content = new $contentEntityClass;
        $formBuilder = $this->createFormBuilder($content, [
            'action' => $this->generateUrl('sherlockode_ac_create_mycontent')
        ]);

        $formBuilderManager->buildCreateContentForm($formBuilder);

        $form = $formBuilder->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $om->persist($content);
            $om->flush();

            return $this->redirectToRoute('sherlockode_ac_edit_mycontent', ['id' => $content->getId()]);
        }

        return $this->render('SherlockodeAdvancedContentBundle:Content:create_content.html.twig', [
            'form' => $form->createView(),
            'data' => $content,
        ]);
    }

    /**
     * @Route("/create/{id}", name="sherlockode_ac_create_mycontent_by_type", defaults={"id"=""})
     *
     * @param int                  $id
     * @param Request              $request
     * @param ObjectManager        $om
     * @param FormBuilderManager   $formBuilderManager
     * @param ContentTypeManager   $contentTypeManager
     * @param ConfigurationManager $configurationManager
     *
     * @return Response
     *
     * @throws EntityNotFoundException
     */
    public function createByTypeAction(
        $id,
        Request $request,
        ObjectManager $om,
        FormBuilderManager $formBuilderManager,
        ContentTypeManager $contentTypeManager,
        ConfigurationManager $configurationManager
    ) {
        $contentType = $contentTypeManager->getContentTypeById($id);

        if ($contentType === null) {
            throw EntityNotFoundException::fromClassNameAndIdentifier(
                $configurationManager->getEntityClass('content_type'),
                [$id]
            );
        }

        $contentEntityClass = $configurationManager->getEntityClass('content');
        $content = new $contentEntityClass;
        $content->setContentType($contentType);

        $formBuilder = $this->createFormBuilder($content, [
            'action' => $this->generateUrl('sherlockode_ac_create_mycontent_by_type', ['id' => $contentType->getId()])
        ]);

        $formBuilderManager->buildContentForm($formBuilder, $content);

        $form = $formBuilder->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $om->persist($content);
            $om->flush();

            return $this->redirectToRoute('sherlockode_ac_edit_mycontent', ['id' => $content->getId()]);
        }

        return $this->render('SherlockodeAdvancedContentBundle:Content:edit_content.html.twig', [
            'form' => $form->createView(),
            'is_ajax' => $request->isXmlHttpRequest(),
        ]);
    }

    /**
     * @Route("/list", name="sherlockode_ac_list_mycontent")
     *
     * @param ContentManager $contentManager
     *
     * @return Response
     */
    public function listAction(ContentManager $contentManager)
    {
        $contents = $contentManager->getContents();

        return $this->render('SherlockodeAdvancedContentBundle:Content:list.html.twig', [
            'contents' => $contents,
        ]);
    }

    /**
     * @Route("/delete/{id}", name="sherlockode_ac_delete_mycontent")
     *
     * @param int                  $id
     * @param ObjectManager        $om
     * @param ContentManager       $contentManager
     * @param ConfigurationManager $configurationManager
     *
     * @return Response
     *
     * @throws EntityNotFoundException
     */
    public function deleteAction(
        $id,
        ObjectManager $om,
        ContentManager $contentManager,
        ConfigurationManager $configurationManager
    ) {
        $content = $contentManager->getContentById($id);

        if ($content === null) {
            throw EntityNotFoundException::fromClassNameAndIdentifier(
                $configurationManager->getEntityClass('content'),
                [$id]
            );
        }

        $om->remove($content);
        $om->flush();

        return $this->redirectToRoute('sherlockode_ac_list_mycontent');
    }

    /**
     * @Route("/view/{id}", name="sherlockode_ac_view_mycontent")
     *
     * @param int                  $id
     * @param ContentManager       $contentManager
     * @param ConfigurationManager $configurationManager
     *
     * @return Response
     *
     * @throws EntityNotFoundException
     */
    public function viewAction(
        $id,
        ContentManager $contentManager,
        ConfigurationManager $configurationManager
    ) {
        $content = $contentManager->getContentById($id);

        if ($content === null) {
            throw EntityNotFoundException::fromClassNameAndIdentifier(
                $configurationManager->getEntityClass('content'),
                [$id]
            );
        }

        return $this->render('SherlockodeAdvancedContentBundle:Content:view.html.twig', [
            'content' => $content,
        ]);
    }
}
