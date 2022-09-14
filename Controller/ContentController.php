<?php

namespace Sherlockode\AdvancedContentBundle\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Sherlockode\AdvancedContentBundle\Form\Type\FieldValueType;
use Sherlockode\AdvancedContentBundle\Manager\ConfigurationManager;
use Sherlockode\AdvancedContentBundle\Manager\ContentManager;
use Sherlockode\AdvancedContentBundle\Manager\FieldManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
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
     * @var FieldManager
     */
    private $fieldManager;

    /**
     * @var ConfigurationManager
     */
    private $configurationManager;

    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * ContentController constructor.
     *
     * @param EntityManagerInterface $em
     * @param ContentManager         $contentManager
     * @param FieldManager           $fieldManager
     * @param ConfigurationManager   $configurationManager
     * @param FormFactoryInterface   $formFactory
     */
    public function __construct(
        EntityManagerInterface $em,
        ContentManager $contentManager,
        FieldManager $fieldManager,
        ConfigurationManager $configurationManager,
        FormFactoryInterface $formFactory
    ) {
        $this->em = $em;
        $this->contentManager = $contentManager;
        $this->fieldManager = $fieldManager;
        $this->configurationManager = $configurationManager;
        $this->formFactory = $formFactory;
    }

    /**
     * @return Response
     */
    public function addFieldAction()
    {
        $fields = $this->fieldManager->getGroupedFieldTypes();

        return $this->render('@SherlockodeAdvancedContent/Content/_select_new_field.html.twig', [
            'fields' => $fields,
        ]);
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function fieldFormAction(Request $request)
    {
        $fieldType = $this->fieldManager->getFieldTypeByCode($request->get('type'));
        $fieldValueClass = $this->configurationManager->getEntityClass('field_value');
        $fieldValue = new $fieldValueClass();
        $fieldValue->setFieldType($fieldType->getCode());
        $formBuilder = $this->formFactory->createNamedBuilder('__field_name__', FieldValueType::class, $fieldValue, [
            'field_type' => $fieldType,
            'action' => $this->generateUrl('sherlockode_acb_content_field_form', ['type' => $fieldType->getCode()]),
            'csrf_protection' => false,
        ]);
        $form = $formBuilder->getForm();
        $form->handleRequest($request);

        if (!$request->query->get('edit') && $form->isSubmitted() && $form->isValid()) {
            return $this->render('@SherlockodeAdvancedContent/Content/_field_preview.html.twig', [
                'label' => $fieldType->getFormFieldLabel(),
                'form' => $form->createView(),
                'fieldId' => 'random-' . rand(10000, 100000),
            ]);
        }

        return $this->render('@SherlockodeAdvancedContent/Content/_edit_field_value.html.twig', [
            'form' => $form->createView(),
            'fieldName' => $fieldType->getFormFieldLabel(),
        ]);
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function duplicateLocaleAction(Request $request)
    {
        $id = $request->get('id');
        $content = $this->contentManager->getContentById($id);

        if ($content === null) {
            throw $this->createNotFoundException(
                sprintf('Entity %s with ID %s not found', $this->configurationManager->getEntityClass('content'), $id)
            );
        }

        $newContent = clone $content;
        $newContent->setLocale($request->get('locale'));

        $this->em->persist($newContent);
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
        $content = $this->contentManager->getContentById($id);

        if ($content === null) {
            throw $this->createNotFoundException(
                sprintf('Entity %s with ID %s not found', $this->configurationManager->getEntityClass('content'), $id)
            );
        }

        $this->em->remove($content);
        $this->em->flush();

        return new RedirectResponse($request->server->get('HTTP_REFERER', '/'));
    }
}
