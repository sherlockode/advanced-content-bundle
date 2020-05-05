<?php

namespace Sherlockode\AdvancedContentBundle\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;
use Sherlockode\AdvancedContentBundle\Form\Type\FlexibleGroupType;
use Sherlockode\AdvancedContentBundle\Manager\ConfigurationManager;
use Sherlockode\AdvancedContentBundle\Manager\ContentManager;
use Sherlockode\AdvancedContentBundle\Model\ContentTypeInterface;
use Sherlockode\AdvancedContentBundle\Model\FieldGroupValueInterface;
use Sherlockode\AdvancedContentBundle\Model\LayoutInterface;
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
     * @param ConfigurationManager   $configurationManager
     * @param FormFactoryInterface   $formFactory
     */
    public function __construct(
        EntityManagerInterface $em,
        ContentManager $contentManager,
        ConfigurationManager $configurationManager,
        FormFactoryInterface $formFactory
    ) {
        $this->em = $em;
        $this->contentManager = $contentManager;
        $this->configurationManager = $configurationManager;
        $this->formFactory = $formFactory;
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function flexibleFormAction(Request $request)
    {
        $contentTypeId = (int) $request->query->get('contentTypeId');
        $layoutId = (int) $request->query->get('layoutId');
        $parentFormId = $request->query->get('parentFormId');
        $contentTypeClass = $this->configurationManager->getEntityClass('content_type');
        $fieldGroupValueClass = $this->configurationManager->getEntityClass('field_group_value');
        $layoutClass = $this->configurationManager->getEntityClass('layout');
        $contentType = $this->getDoctrine()->getRepository($contentTypeClass)->find($contentTypeId);
        $layout = $this->getDoctrine()->getRepository($layoutClass)->find($layoutId);
        if (!$layout instanceof LayoutInterface || !$contentType instanceof ContentTypeInterface) {
            throw $this->createNotFoundException();
        }

        $name = '__flexible_name__';
        /** @var FieldGroupValueInterface $group */
        $group = new $fieldGroupValueClass();
        $group->setLayout($layout);

        $formBuilder = $this->formFactory->createNamedBuilder($name, FlexibleGroupType::class, $group, [
            'contentType' => $contentType,
            'csrf_protection' => false,
            'parentFormId' => $parentFormId,
        ]);
        $form = $formBuilder->getForm();

        return $this->render('@SherlockodeAdvancedContent/Content/flexible_field_value.html.twig', [
            'form' => $form->createView(),
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
