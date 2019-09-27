<?php

namespace Sherlockode\AdvancedContentBundle\Controller;

use Sherlockode\AdvancedContentBundle\Form\Type\ExportType;
use Sherlockode\AdvancedContentBundle\Form\Type\ImportType;
use Sherlockode\AdvancedContentBundle\Manager\ExportManager;
use Sherlockode\AdvancedContentBundle\Manager\ImportManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Contracts\Translation\TranslatorInterface;

class ToolsController extends AbstractController
{
    /**
     * @var ImportManager
     */
    private $importManager;

    /**
     * @var ExportManager
     */
    private $exportManager;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var string
     */
    private $template;

    /**
     * @param ImportManager       $importManager
     * @param ExportManager       $exportManager
     * @param TranslatorInterface $translator
     * @param string              $template
     */
    public function __construct(
        ImportManager $importManager,
        ExportManager $exportManager,
        TranslatorInterface $translator,
        $template
    ) {
        $this->importManager = $importManager;
        $this->exportManager = $exportManager;
        $this->translator = $translator;
        $this->template = $template;
    }

    /**
     * @return Response
     *
     * @throws AccessDeniedException
     */
    public function indexAction()
    {
        if (!$this->isGranted('ROLE_SHERLOCKODE_ADVANCED_CONTENT_TOOLS')) {
            throw $this->createAccessDeniedException();
        }

        $importForm = $this->createForm(ImportType::class, null, [
            'action' => $this->generateUrl('sherlockode_acb_tools_import'),
        ]);

        $exportForm = $this->createForm(ExportType::class, null, [
            'action' => $this->generateUrl('sherlockode_acb_tools_export'),
        ]);

        return $this->render($this->template, [
            'importForm' => $importForm->createView(),
            'exportForm' => $exportForm->createView(),
        ]);
    }

    /**
     * @param Request $request
     *
     * @return Response
     *
     * @throws AccessDeniedException
     */
    public function importAction(Request $request)
    {
        if (!$this->isGranted('ROLE_SHERLOCKODE_ADVANCED_CONTENT_TOOLS')) {
            throw $this->createAccessDeniedException();
        }

        $form = $this->createForm(ImportType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('file')->getData();

            try {
                $this->importManager->addFileToProcess($file);
                $results = $this->importManager->processData();
                foreach ($results as $result) {
                    foreach ($result->getMessages() as $message) {
                        $this->addFlash('error', $message);
                    }
                }
                $this->addFlash('success', $this->translator->trans('tools.import.success', [], 'AdvancedContentBundle'));
            } catch (\Exception $e) {
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->redirectToRoute('sherlockode_acb_tools_index');
    }

    /**
     * @param Request $request
     *
     * @return Response
     *
     * @throws AccessDeniedException
     */
    public function exportAction(Request $request)
    {
        if (!$this->isGranted('ROLE_SHERLOCKODE_ADVANCED_CONTENT_TOOLS')) {
            throw $this->createAccessDeniedException();
        }

        $form = $this->createForm(ExportType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $contentTypes = $form->get('contentType')->getData();
                $this->exportManager->generateContentTypesData($contentTypes);
                $pages = $form->get('page')->getData();
                $this->exportManager->generatePagesData($pages);
                $contents = $form->get('content')->getData();
                $this->exportManager->generateContentsData($contents);
                $zipFileName = $this->exportManager->generateZipFile();

                if (!file_exists($zipFileName)) {
                    throw new \Exception($this->translator->trans('tools.export.error_empty_selection', [], 'AdvancedContentBundle'));
                }

                $response = new Response(file_get_contents($zipFileName));
                $disposition = $response->headers->makeDisposition(
                    ResponseHeaderBag::DISPOSITION_ATTACHMENT,
                    basename($zipFileName)
                );
                $response->headers->set('Content-Disposition', $disposition);

                unlink($zipFileName);

                return $response;
            } catch (\Exception $e) {
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->redirectToRoute('sherlockode_acb_tools_index');
    }
}
