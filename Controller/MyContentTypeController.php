<?php

namespace Sherlockode\AdvancedContentBundle\Controller;

use Doctrine\ORM\EntityNotFoundException;
use Sherlockode\AdvancedContentBundle\Form\DataTransformer\StringToArrayTransformer;
use Sherlockode\AdvancedContentBundle\Form\Type\FieldCreateType;
use Sherlockode\AdvancedContentBundle\Manager\ConfigurationManager;
use Sherlockode\AdvancedContentBundle\Manager\ContentTypeManager;
use Sherlockode\AdvancedContentBundle\Manager\FieldManager;
use Sherlockode\AdvancedContentBundle\Manager\FormBuilderManager;
use Sherlockode\AdvancedContentBundle\Model\FieldInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\HttpFoundation\JsonResponse;
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
            'action' => $this->generateUrl('sherlockode_ac_edit_mycontenttype', ['id' => $contentType->getId()]),
            'attr' => [
                'data-change-type-url' => $this->generateUrl('sherlockode_ac_change_field_type'),
                'class' => 'edit-content-type'
            ],
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

    /**
     * @Route("/add-field-type", name="sherlockode_ac_content_type_add_field_type")
     *
     * @param Request              $request
     * @param ObjectManager        $om
     * @param FormBuilderManager   $formBuilderManager
     * @param ConfigurationManager $configurationManager
     * @param FieldManager         $fieldManager
     * @param ContentTypeManager   $contentTypeManager
     *
     * @return Response
     */
    public function addFieldAction(
        Request $request,
        ObjectManager $om,
        FormBuilderManager $formBuilderManager,
        ConfigurationManager $configurationManager,
        FieldManager $fieldManager,
        ContentTypeManager $contentTypeManager
    ) {
        $id = $request->get('contentTypeId');

        $fieldClass = $configurationManager->getEntityClass('field');
        $field = new $fieldClass;
        $fieldTypeChoices = ['Select field type' => ''];
        $fieldTypeChoices = array_merge($fieldTypeChoices, $fieldManager->getFieldTypeFormChoices());

        $formOptions = [
            'action' => $this->generateUrl('sherlockode_ac_content_type_add_field_type'),
            'attr' => ['class' => 'form-create-field'],
            'data_class' => $configurationManager->getEntityClass('field'),
            'type_choices' => $fieldTypeChoices,
        ];

        $addFieldForm = $this->createForm(FieldCreateType::class, $field, $formOptions);
        $addFieldForm->handleRequest($request);

        if ($addFieldForm->isSubmitted()) {
            if ($addFieldForm->isValid()) {
                $contentType = $contentTypeManager->getContentTypeById($id);
                $field->setIsRequired(false);
                $field->setContentType($contentType);
                $om->persist($field);
                $om->flush();

                $formBuilder = $this->createFormBuilder();
                $formBuilderManager->buildSingleContentTypeFieldForm($formBuilder, $field);
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
                'html'    => $this->renderView('SherlockodeAdvancedContentBundle:ContentType:add_field_form.html.twig', [
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
     * @Route("/delete-field", name="sherlockode_ac_delete_field")
     *
     * @param Request            $request
     * @param ObjectManager      $om
     * @param ContentTypeManager $contentTypeManager
     *
     * @return Response
     */
    public function deleteFieldAction(
        Request $request,
        ObjectManager $om,
        ContentTypeManager $contentTypeManager
    ) {
        if (!$request->isXmlHttpRequest()) {
            throw $this->createAccessDeniedException();
        }

        $id = $request->get('id');
        $fieldId = $request->get('fieldId');

        $contentType = $contentTypeManager->getContentTypeById($id);

        if ($contentType === null) {
            throw $this->createNotFoundException('Unable to find content type');
        }

        foreach ($contentType->getFields() as $field) {
            if ($field->getId() == $fieldId) {
                $om->remove($field);
                $om->flush();

                return new JsonResponse();
            }
        }

        throw $this->createNotFoundException('Unable to find field');
    }

    /**
     * @Route("/change-field-type", name="sherlockode_ac_change_field_type")
     *
     * @param Request            $request
     * @param FieldManager       $fieldManager
     * @param ContentTypeManager $contentTypeManager
     *
     * @return Response
     */
    public function changeFieldTypeAction(
        Request $request,
        FieldManager $fieldManager,
        ContentTypeManager $contentTypeManager
    ) {
        if (!$request->isXmlHttpRequest()) {
            throw $this->createAccessDeniedException();
        }

        $type = $request->get('type');
        $fieldType = $fieldManager->getFieldTypeByCode($type);

        $contentTypeId = $request->get('contentTypeId');
        $contentType = $contentTypeManager->getContentTypeById($contentTypeId);
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
            ->add($slug, FormType::class)
        ;

        $formBuilder
            ->get('fields')
            ->get($slug)
            ->add('options', FormType::class);

        $fieldType->addFieldOptions($formBuilder->get('fields')->get($slug));
        $formBuilder->get('fields')->get($slug)->get('options')->addModelTransformer(new StringToArrayTransformer());

        $response = [
            'success' => 1,
            'html' => $this->renderView('SherlockodeAdvancedContentBundle:ContentType:field_options.html.twig', [
                'form' => $formBuilder->getForm()->createView(),
                'slug' => $slug,
            ])
        ];
        return new JsonResponse($response);
    }
}
