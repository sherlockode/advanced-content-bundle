<?php

namespace Sherlockode\AdvancedContentBundle\Controller;

use Doctrine\ORM\EntityNotFoundException;
use Sherlockode\AdvancedContentBundle\Form\Type\ContentTypeFormType;
use Sherlockode\AdvancedContentBundle\Manager\ConfigurationManager;
use Sherlockode\AdvancedContentBundle\Manager\ContentTypeManager;
use Sherlockode\AdvancedContentBundle\Manager\FieldManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class MyContentTypeController
 */
class MyContentTypeController extends Controller
{
    /**
     * @var ObjectManager
     */
    private $om;

    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @var ContentTypeManager
     */
    private $contentTypeManager;

    /**
     * @var ConfigurationManager
     */
    private $configurationManager;

    /**
     * @var FieldManager
     */
    private $fieldManager;

    /**
     * MyContentTypeController constructor.
     *
     * @param ObjectManager        $om
     * @param FormFactoryInterface $formFactory
     * @param ContentTypeManager   $contentTypeManager
     * @param ConfigurationManager $configurationManager
     * @param FieldManager         $fieldManager
     */
    public function __construct(
        ObjectManager $om,
        FormFactoryInterface $formFactory,
        ContentTypeManager $contentTypeManager,
        ConfigurationManager $configurationManager,
        FieldManager $fieldManager
    ) {
        $this->om = $om;
        $this->formFactory = $formFactory;
        $this->contentTypeManager = $contentTypeManager;
        $this->configurationManager = $configurationManager;
        $this->fieldManager = $fieldManager;
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
        $contentType = $this->contentTypeManager->getContentTypeById($id);

        if ($contentType === null) {
            throw EntityNotFoundException::fromClassNameAndIdentifier(
                $this->configurationManager->getEntityClass('content_type'),
                [$id]
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
            $this->om->flush();

            return $this->redirectToRoute('sherlockode_acb_content_type_edit', ['id' => $contentType->getId()]);
        }

        return $this->render('@SherlockodeAdvancedContent/ContentType/edit_content_type.html.twig', [
            'form' => $form->createView(),
            'data' => $contentType,
        ]);
    }

    /**
     * @param Request              $request
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
            $this->om->persist($contentType);
            $this->om->flush();

            return $this->redirectToRoute('sherlockode_acb_content_type_edit', ['id' => $contentType->getId()]);
        }

        return $this->render('@SherlockodeAdvancedContent/ContentType/create_content_type.html.twig', [
            'form' => $form->createView(),
            'data' => $contentType,
        ]);
    }

    /**
     * @param string $formName
     *
     * @return array
     */
    private function getFormPath($formName)
    {
        $formPath = preg_split("/(\[|\])/", $formName);
        foreach ($formPath as $key => $value) {
            if ($value === '') {
                unset($formPath[$key]);
            }
        }
        return $formPath;
    }

    /**
     * @param array                $path
     * @param FormBuilderInterface $formBuilder
     *
     * @return FormBuilderInterface
     */
    private function createEmbeddedForm($path, $formBuilder)
    {
        $currentPath = array_shift($path);
        while ($currentPath !== null) {
            $formBuilder
                ->add($currentPath, FormType::class);
            $formBuilder = $formBuilder->get($currentPath);

            $currentPath = array_shift($path);
        }

        return $formBuilder;
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function changeFieldTypeAction(Request $request)
    {
        if (!$request->isXmlHttpRequest()) {
            throw $this->createAccessDeniedException();
        }

        $type = $request->get('type');
        $fieldType = $this->fieldManager->getFieldTypeByCode($type);

        $formName = $request->get('formPath');

        $formPath = $this->getFormPath($formName);
        // Extract first item to create named builder
        $rootPath = array_shift($formPath);
        $formChildren = $formPath;
        $rootFormBuilder = $this->formFactory->createNamedBuilder($rootPath, FormType::class, null, ['translation_domain' => 'AdvancedContentBundle']);
        // Create formBuilder recursively, according to formName
        $formBuilder = $this->createEmbeddedForm($formPath, $rootFormBuilder);
        $formBuilder->add('options', FormType::class);

        $fieldType->addFieldOptions($formBuilder);

        $form = $rootFormBuilder->getForm();
        foreach ($formChildren as $child) {
            $form = $form->get($child);
        }
        $formView = $form->createView();

        $response = [
            'success' => 1,
            'optionHtml'    => $this->renderView('@SherlockodeAdvancedContent/ContentType/field_options_render.html.twig', [
                'options' => $formView['options'],
            ]),
            'layoutHtml'    => $this->renderView('@SherlockodeAdvancedContent/ContentType/field_layout.html.twig', [
                'form' => $formView,
            ])
        ];

        return new JsonResponse($response);
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
     * @param int                  $id
     *
     * @return Response
     *
     * @throws EntityNotFoundException
     */
    public function deleteAction($id)
    {
        $contentType = $this->contentTypeManager->getContentTypeById($id);

        if ($contentType === null) {
            throw EntityNotFoundException::fromClassNameAndIdentifier(
                $this->configurationManager->getEntityClass('content_type'),
                [$id]
            );
        }

        $this->om->remove($contentType);
        $this->om->flush();

        return $this->redirectToRoute('sherlockode_acb_content_type_list');
    }
}
