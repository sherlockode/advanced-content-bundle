<?php

namespace Sherlockode\AdvancedContentBundle\Controller;

use Doctrine\ORM\EntityNotFoundException;
use Sherlockode\AdvancedContentBundle\Form\DataTransformer\StringToArrayTransformer;
use Sherlockode\AdvancedContentBundle\Form\Type\FieldCreateType;
use Sherlockode\AdvancedContentBundle\Manager\ConfigurationManager;
use Sherlockode\AdvancedContentBundle\Manager\ContentTypeManager;
use Sherlockode\AdvancedContentBundle\Manager\FieldManager;
use Sherlockode\AdvancedContentBundle\Manager\FormBuilderManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\FormType;
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
     * @var FormBuilderManager
     */
    private $formBuilderManager;

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
     * @param FormBuilderManager   $formBuilderManager
     * @param ContentTypeManager   $contentTypeManager
     * @param ConfigurationManager $configurationManager
     * @param FieldManager         $fieldManager
     */
    public function __construct(
        ObjectManager $om,
        FormBuilderManager $formBuilderManager,
        ContentTypeManager $contentTypeManager,
        ConfigurationManager $configurationManager,
        FieldManager $fieldManager
    ) {
        $this->om = $om;
        $this->formBuilderManager = $formBuilderManager;
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

        $formBuilder = $this->createFormBuilder($contentType, [
            'action' => $this->generateUrl('sherlockode_acb_content_type_edit', ['id' => $contentType->getId()]),
            'attr' => [
                'data-change-type-url' => $this->generateUrl('sherlockode_acb_content_type_change_field_type'),
                'class' => 'edit-content-type'
            ],
        ]);

        $this->formBuilderManager->buildContentTypeForm($formBuilder, $contentType);

        $form = $formBuilder->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->contentTypeManager->processFieldsChangeType($contentType, $fieldTypes);
            $this->om->flush();

            return $this->redirectToRoute('sherlockode_acb_content_type_edit', ['id' => $contentType->getId()]);
        }

        return $this->render('SherlockodeAdvancedContentBundle:ContentType:edit_content_type.html.twig', [
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
        $formBuilder = $this->createFormBuilder($contentType, [
            'action' => $this->generateUrl('sherlockode_acb_content_type_create')
        ]);

        $this->formBuilderManager->buildCreateContentTypeForm($formBuilder);

        $form = $formBuilder->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->om->persist($contentType);
            $this->om->flush();

            return $this->redirectToRoute('sherlockode_acb_content_type_edit', ['id' => $contentType->getId()]);
        }

        return $this->render('SherlockodeAdvancedContentBundle:ContentType:create_content_type.html.twig', [
            'form' => $form->createView(),
            'data' => $contentType,
        ]);
    }

    /**
     * @param Request              $request
     *
     * @return Response
     */
    public function addFieldAction(Request $request)
    {
        $id = $request->get('contentTypeId');

        $fieldClass = $this->configurationManager->getEntityClass('field');
        $field = new $fieldClass;
        $fieldTypeChoices = ['Select field type' => ''];
        $fieldTypeChoices = array_merge($fieldTypeChoices, $this->fieldManager->getFieldTypeFormChoices());

        $formOptions = [
            'action' => $this->generateUrl('sherlockode_acb_content_type_add_field', ['contentTypeId' => $id]),
            'attr' => ['class' => 'form-create-field'],
            'data_class' => $fieldClass,
            'type_choices' => $fieldTypeChoices,
        ];

        $addFieldForm = $this->createForm(FieldCreateType::class, $field, $formOptions);
        $addFieldForm->handleRequest($request);

        if ($addFieldForm->isSubmitted()) {
            if ($addFieldForm->isValid()) {
                $contentType = $this->contentTypeManager->getContentTypeById($id);
                $field->setIsRequired(false);
                $field->setContentType($contentType);
                $field->setSortOrder($this->contentTypeManager->getNewFieldSortOrder($contentType));
                $this->om->persist($field);
                $this->om->flush();

                $formBuilder = $this->createFormBuilder();
                $this->formBuilderManager->buildSingleContentTypeFieldForm($formBuilder, $field);
                $form = $formBuilder->getForm();

                return new JsonResponse([
                    'success' => 1,
                    'html'    => $this->renderView('SherlockodeAdvancedContentBundle:ContentType:new_field.html.twig', [
                        'form' => $form->createView(),
                    ])
                ]);
            }

            return new JsonResponse([
                'success' => 0,
                'html' => $this->renderView('SherlockodeAdvancedContentBundle:ContentType:add_field_form.html.twig', [
                    'form' => $addFieldForm->createView(),
                    'contentTypeId' => $id,
                ])
            ]);
        }

        return $this->render('SherlockodeAdvancedContentBundle:ContentType:add_field_form.html.twig', [
            'form' => $addFieldForm->createView(),
            'contentTypeId' => $id,
        ]);
    }

    /**
     * @param Request            $request
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

        $contentTypeId = $request->get('contentTypeId');
        $contentType = $this->contentTypeManager->getContentTypeById($contentTypeId);
        $fieldId = $request->get('fieldId');

        if ($contentType === null) {
            throw $this->createNotFoundException('Unable to find content type');
        }

        $field = null;
        foreach ($contentType->getFields() as $contentTypeField) {
            if ($contentTypeField->getId() == $fieldId) {
                $field = $contentTypeField;
            }
        }

        if ($field === null) {
            throw $this->createNotFoundException('Unable to find field');
        }

        $slug = $field->getSlug();

        $formBuilder = $this->createFormBuilder();
        $formBuilder
            ->add('fields', FormType::class);

        $formBuilder
            ->get('fields')
            ->add($slug, FormType::class);

        $formBuilder
            ->get('fields')
            ->get($slug)
            ->add('options', FormType::class);

        $fieldType->addFieldOptions($formBuilder->get('fields')->get($slug));
        $formBuilder->get('fields')->get($slug)->get('options')->addModelTransformer(new StringToArrayTransformer());

        $response = [
            'success' => 1,
            'html'    => $this->renderView('SherlockodeAdvancedContentBundle:ContentType:field_options.html.twig', [
                'form' => $formBuilder->getForm()->createView(),
                'slug' => $slug,
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

        return $this->render('SherlockodeAdvancedContentBundle:ContentType:list.html.twig', [
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
