<?php

namespace Sherlockode\AdvancedContentBundle\Controller;

use Doctrine\ORM\EntityNotFoundException;
use Sherlockode\AdvancedContentBundle\Manager\ConfigurationManager;
use Sherlockode\AdvancedContentBundle\Manager\ContentManager;
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
}
