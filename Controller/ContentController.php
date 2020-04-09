<?php

namespace Sherlockode\AdvancedContentBundle\Controller;

use Sherlockode\AdvancedContentBundle\Form\Type\FlexibleGroupType;
use Sherlockode\AdvancedContentBundle\Manager\ConfigurationManager;
use Sherlockode\AdvancedContentBundle\Model\ContentTypeInterface;
use Sherlockode\AdvancedContentBundle\Model\FieldGroupValueInterface;
use Sherlockode\AdvancedContentBundle\Model\LayoutInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ContentController
 */
class ContentController extends AbstractController
{
    /**
     * @var ConfigurationManager
     */
    private $configurationManager;

    /**
     * ContentController constructor.
     *
     * @param ConfigurationManager   $configurationManager
     */
    public function __construct(
        ConfigurationManager $configurationManager
    ) {
        $this->configurationManager = $configurationManager;
    }

    /**
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

        $formBuilder = $this->get('form.factory')->createNamedBuilder($name, FlexibleGroupType::class, $group, [
            'contentType' => $contentType,
            'csrf_protection' => false,
            'parentFormId' => $parentFormId,
        ]);
        $form = $formBuilder->getForm();

        return $this->render('@SherlockodeAdvancedContent/Content/flexible_field_value.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
