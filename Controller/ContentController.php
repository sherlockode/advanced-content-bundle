<?php

namespace Sherlockode\AdvancedContentBundle\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Sherlockode\AdvancedContentBundle\Form\Type\ElementType;
use Sherlockode\AdvancedContentBundle\Manager\ConfigurationManager;
use Sherlockode\AdvancedContentBundle\Manager\ContentManager;
use Sherlockode\AdvancedContentBundle\Manager\ElementManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

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
     * @var ElementManager
     */
    private $elementManager;

    /**
     * @var ConfigurationManager
     */
    private $configurationManager;

    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * ContentController constructor.
     *
     * @param EntityManagerInterface $em
     * @param ContentManager         $contentManager
     * @param ElementManager         $elementManager
     * @param ConfigurationManager   $configurationManager
     * @param FormFactoryInterface   $formFactory
     * @param TranslatorInterface    $translator
     */
    public function __construct(
        EntityManagerInterface $em,
        ContentManager         $contentManager,
        ElementManager         $elementManager,
        ConfigurationManager   $configurationManager,
        FormFactoryInterface   $formFactory,
        TranslatorInterface    $translator
    ) {
        $this->em = $em;
        $this->contentManager = $contentManager;
        $this->elementManager = $elementManager;
        $this->configurationManager = $configurationManager;
        $this->formFactory = $formFactory;
        $this->translator = $translator;
    }

    /**
     * @return Response
     */
    public function addFieldAction()
    {
        $fields = $this->elementManager->getGroupedFieldTypes();

        return new JsonResponse([
            'title' => $this->translator->trans('content.add_field', [], 'AdvancedContentBundle'),
            'content' => $this->renderView('@SherlockodeAdvancedContent/Content/_select_new_field.html.twig', [
                'fields' => $fields,
            ]),
        ]);
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function fieldFormAction(Request $request)
    {
        $element = $this->elementManager->getElementByCode($request->get('type'));
        $elementData = [];
        $elementData['elementType'] = $element->getCode();
        $formBuilder = $this->formFactory->createNamedBuilder('__field_name__', ElementType::class, $elementData, [
            'element_type' => $element,
            'action' => $this->generateUrl('sherlockode_acb_content_field_form', ['type' => $element->getCode()]),
            'csrf_protection' => false,
            'label' => $element->getFormFieldLabel(),
        ]);
        $form = $formBuilder->getForm();
        $form->handleRequest($request);

        if (!$request->query->get('edit') && $form->isSubmitted()) {
            if ($form->isValid()) {
                // Rebuild form for row and columns
                // Because data is being rearranged on submit
                // Otherwise posted elements cannot be matched with form children
                if ($element->getCode() === 'row' || $element->getCode() === 'column') {
                    $formBuilder = $this->formFactory->createNamedBuilder('__field_name__', ElementType::class, $form->getData(), [
                        'element_type'    => $element,
                        'action'          => $this->generateUrl('sherlockode_acb_content_field_form', ['type' => $element->getCode()]),
                        'csrf_protection' => false,
                        'label'           => $element->getFormFieldLabel(),
                    ]);
                    $form = $formBuilder->getForm();
                }

                return new JsonResponse([
                    'success' => true,
                    'preview' => $this->renderView('@SherlockodeAdvancedContent/Content/_field_preview.html.twig', [
                        'form' => $form->createView(),
                    ]),
                    'form' => $this->renderView('@SherlockodeAdvancedContent/Content/_field_form.html.twig', [
                        'form' => $form->createView(),
                    ]),
                ]);
            } else {
                return new JsonResponse([
                    'success' => false,
                    'content' => $this->renderView('@SherlockodeAdvancedContent/Content/_edit_element.html.twig', [
                        'form' => $form->createView(),
                    ]),
                ]);
            }
        }

        return new JsonResponse([
            'title' => $this->translator->trans($element->getFormFieldLabel(), [], 'AdvancedContentBundle'),
            'content' => $this->renderView('@SherlockodeAdvancedContent/Content/_edit_element.html.twig', [
                'form' => $form->createView(),
            ]),
            'footer' => $this->renderView('@SherlockodeAdvancedContent/Content/_button_submit_slide.html.twig', [
                'form' => $form->createView(),
            ]),
        ]);
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function duplicateAction(Request $request)
    {
        $id = $request->get('id');
        $content = $this->contentManager->getContentById($id);

        if ($content === null) {
            throw $this->createNotFoundException(
                sprintf('Entity %s with ID %s not found', $this->configurationManager->getEntityClass('content'), $id)
            );
        }

        $newContent = $this->contentManager->duplicate($content);
        $this->em->persist($newContent);
        $this->em->flush();

        return new RedirectResponse($request->server->get('HTTP_REFERER', '/'));
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
