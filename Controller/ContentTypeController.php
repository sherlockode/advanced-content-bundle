<?php

namespace Sherlockode\AdvancedContentBundle\Controller;

use Sherlockode\AdvancedContentBundle\Manager\FieldManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ContentTypeController
 */
class ContentTypeController extends AbstractController
{
    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @var FieldManager
     */
    private $fieldManager;

    /**
     * @param FormFactoryInterface   $formFactory
     * @param FieldManager           $fieldManager
     */
    public function __construct(
        FormFactoryInterface $formFactory,
        FieldManager $fieldManager
    ) {
        $this->formFactory = $formFactory;
        $this->fieldManager = $fieldManager;
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function changeFieldTypeAction(Request $request)
    {
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
}
