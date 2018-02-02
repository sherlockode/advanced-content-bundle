<?php

namespace Sherlockode\AdvancedContentBundle\Controller;

use AppBundle\Entity\Content;
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
     * @param Request            $request
     * @param ObjectManager      $om
     * @param Content            $content
     * @param FormBuilderManager $formBuilderManager
     *
     * @return Response
     */
    public function editAction(Request $request, ObjectManager $om, Content $content, FormBuilderManager $formBuilderManager)
    {
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
     * @param Request            $request
     * @param ObjectManager      $om
     * @param FormBuilderManager $formBuilderManager
     *
     * @return Response
     */
    public function createAction(Request $request, ObjectManager $om, FormBuilderManager $formBuilderManager)
    {
        $content = new Content();
        $formBuilder = $this->createFormBuilder($content, ['action' => $this->generateUrl('sherlockode_ac_create_mycontent')]);

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
