<?php

namespace Sherlockode\AdvancedContentBundle\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Sherlockode\AdvancedContentBundle\Form\Type\ContentTypeFormType;
use Sherlockode\AdvancedContentBundle\Manager\ConfigurationManager;
use Sherlockode\AdvancedContentBundle\Manager\ContentTypeManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class MyContentTypeController
 */
class MyContentTypeController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var ContentTypeManager
     */
    private $contentTypeManager;

    /**
     * @var ConfigurationManager
     */
    private $configurationManager;

    /**
     * MyContentTypeController constructor.
     *
     * @param EntityManagerInterface $em
     * @param ContentTypeManager     $contentTypeManager
     * @param ConfigurationManager   $configurationManager
     */
    public function __construct(
        EntityManagerInterface $em,
        ContentTypeManager $contentTypeManager,
        ConfigurationManager $configurationManager
    ) {
        $this->em = $em;
        $this->contentTypeManager = $contentTypeManager;
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
        $contentType = $this->contentTypeManager->getContentTypeById($id);

        if ($contentType === null) {
            throw $this->createNotFoundException(
                sprintf('Entity %s with ID %s not found', $this->configurationManager->getEntityClass('content_type'), $id)
            );
        }

        $fieldTypes = [];
        foreach ($contentType->getFields() as $field) {
            $fieldTypes[$field->getId()] = $field->getType();
        }

        $form = $this->createForm(ContentTypeFormType::class, $contentType, [
            'action' => $this->generateUrl('sherlockode_acb_content_type_edit', ['id' => $contentType->getId()]),
            'attr' => [
                'data-change-type-url' => $this->generateUrl('sherlockode_acb_content_type_change_field_type'),
                'class' => 'edit-content-type'
            ],
            'contentType' => $contentType,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->contentTypeManager->processFieldsChangeType($contentType, $fieldTypes);
            $this->em->flush();

            return $this->redirectToRoute('sherlockode_acb_content_type_edit', ['id' => $contentType->getId()]);
        }

        return $this->render('@SherlockodeAdvancedContent/ContentType/edit_content_type.html.twig', [
            'form' => $form->createView(),
            'data' => $contentType,
        ]);
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function createAction(Request $request)
    {
        $contentTypeEntityClass = $this->configurationManager->getEntityClass('content_type');
        $contentType = new $contentTypeEntityClass;

        $form = $this->createForm(ContentTypeFormType::class, $contentType, [
            'action' => $this->generateUrl('sherlockode_acb_content_type_create'),
            'contentType' => $contentType,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($contentType);
            $this->em->flush();

            return $this->redirectToRoute('sherlockode_acb_content_type_edit', ['id' => $contentType->getId()]);
        }

        return $this->render('@SherlockodeAdvancedContent/ContentType/create_content_type.html.twig', [
            'form' => $form->createView(),
            'data' => $contentType,
        ]);
    }

    /**
     * @return Response
     */
    public function listAction()
    {
        $contentTypes = $this->contentTypeManager->getContentTypes();

        return $this->render('@SherlockodeAdvancedContent/ContentType/list.html.twig', [
            'contentTypes' => $contentTypes,
        ]);
    }

    /**
     * @param int $id
     *
     * @return Response
     */
    public function deleteAction($id)
    {
        $contentType = $this->contentTypeManager->getContentTypeById($id);

        if ($contentType === null) {
            throw $this->createNotFoundException(
                sprintf('Entity %s with ID %s not found', $this->configurationManager->getEntityClass('content_type'), $id)
            );
        }

        $this->em->remove($contentType);
        $this->em->flush();

        return $this->redirectToRoute('sherlockode_acb_content_type_list');
    }
}
