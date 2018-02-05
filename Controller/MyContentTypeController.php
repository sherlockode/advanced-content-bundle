<?php

namespace Sherlockode\AdvancedContentBundle\Controller;

use Doctrine\ORM\EntityNotFoundException;
use Sherlockode\AdvancedContentBundle\Manager\ConfigurationManager;
use Sherlockode\AdvancedContentBundle\Manager\ContentTypeManager;
use Sherlockode\AdvancedContentBundle\Manager\FormBuilderManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class MyContentTypeController
 *
 * @Route("/mycontent-type")
 */
class MyContentTypeController extends Controller
{
    /**
     * @Route("/{id}/update", name="sherlockode_ac_edit_mycontenttype")
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
    public function editAction(
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

        $formBuilder = $this->createFormBuilder($contentType, [
            'action' => $this->generateUrl('sherlockode_ac_edit_mycontenttype', ['id' => $contentType->getId()])
        ]);

        $formBuilderManager->buildContentTypeForm($formBuilder, $contentType);

        $form = $formBuilder->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $om->flush();

            return $this->redirectToRoute('sherlockode_ac_edit_mycontenttype', ['id' => $contentType->getId()]);
        }

        return $this->render('SherlockodeAdvancedContentBundle:ContentType:edit_content_type.html.twig', [
            'form' => $form->createView(),
            'data' => $contentType,
        ]);
    }

    /**
     * @Route("/create", name="sherlockode_ac_create_mycontenttype")
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
        $contentTypeEntityClass = $configurationManager->getEntityClass('content_type');
        $contentType = new $contentTypeEntityClass;
        $formBuilder = $this->createFormBuilder($contentType, [
            'action' => $this->generateUrl('sherlockode_ac_create_mycontenttype')
        ]);

        $formBuilderManager->buildCreateContentTypeForm($formBuilder);

        $form = $formBuilder->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $om->persist($contentType);
            $om->flush();

            return $this->redirectToRoute('sherlockode_ac_edit_mycontenttype', ['id' => $contentType->getId()]);
        }

        return $this->render('SherlockodeAdvancedContentBundle:ContentType:create_content_type.html.twig', [
            'form' => $form->createView(),
            'data' => $contentType,
        ]);
    }
}
