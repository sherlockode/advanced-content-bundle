<?php

declare(strict_types=1);

namespace Sherlockode\AdvancedContentBundle\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Doctrine\ORM\EntityManagerInterface;
use Sherlockode\AdvancedContentBundle\Form\Type\ExportType;
use Sherlockode\AdvancedContentBundle\Form\Type\ImportType;
use Sherlockode\AdvancedContentBundle\Form\Type\PageTypeType;
use Sherlockode\AdvancedContentBundle\Form\Type\ScopeType;
use Sherlockode\AdvancedContentBundle\Manager\ConfigurationManager;
use Sherlockode\AdvancedContentBundle\Manager\ExportManager;
use Sherlockode\AdvancedContentBundle\Manager\ImportManager;
use Sherlockode\AdvancedContentBundle\Model\PageTypeInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Contracts\Translation\TranslatorInterface;

class ToolsController extends AbstractController
{
    /**
     * @param string                 $template
     */
    public function __construct(private readonly ImportManager $importManager, private readonly ExportManager $exportManager, private readonly TranslatorInterface $translator, private readonly ConfigurationManager $configurationManager, private readonly EntityManagerInterface $em, private $template)
    {
    }

    public function index(Request $request): RedirectResponse|Response
    {
        $importForm = $this->createForm(ImportType::class, null, [
            'action' => $this->generateUrl('sherlockode_acb_tools_import'),
        ]);

        $exportForm = $this->createForm(ExportType::class, null, [
            'action' => $this->generateUrl('sherlockode_acb_tools_export'),
        ]);

        $pageTypeClass = $this->configurationManager->getEntityClass('page_type');
        $pageTypes = $this->em->getRepository($pageTypeClass)->findAll();
        $pageType = new $pageTypeClass;
        $pageTypeForm = $this->createForm(PageTypeType::class, $pageType, [
            'action' => $this->generateUrl('sherlockode_acb_tools_index'),
        ]);
        $pageTypeForm->handleRequest($request);
        if ($pageTypeForm->isSubmitted() && $pageTypeForm->isValid()) {
            $existingPageTypes = $this->em->getRepository($pageTypeClass)->findBy([
                'name' => $pageType->getName(),
            ]);
            if (count($existingPageTypes) === 0) {
                $this->em->persist($pageType);
                $this->em->flush();

                return $this->redirectToRoute('sherlockode_acb_tools_index');
            }
            $pageTypeForm->addError(new FormError(
                $this->translator->trans('page_type.errors.unique_name', [], 'AdvancedContentBundle')
            ));
        }

        $scopeClass = $this->configurationManager->getEntityClass('scope');
        $scopes = $this->em->getRepository($scopeClass)->findAll();
        $scope = new $scopeClass;
        $scopeForm = $this->createForm(ScopeType::class, $scope, [
            'action' => $this->generateUrl('sherlockode_acb_tools_index'),
        ]);
        $scopeForm->handleRequest($request);
        if ($scopeForm->isSubmitted() && $scopeForm->isValid()) {
            $existingScopes = $this->em->getRepository($scopeClass)->findBy([
                'locale' => $scope->getLocale(),
            ]);
            if (count($existingScopes) === 0) {
                $this->em->persist($scope);
                $this->em->flush();

                return $this->redirectToRoute('sherlockode_acb_tools_index');
            }
            $scopeForm->addError(new FormError(
                $this->translator->trans('scope.errors.unique_locale', [], 'AdvancedContentBundle')
            ));
        }

        return $this->render($this->template, [
            'importForm' => $importForm->createView(),
            'exportForm' => $exportForm->createView(),
            'pageTypes' => $pageTypes,
            'pageTypeForm' => $pageTypeForm->createView(),
            'scopes' => $scopes,
            'scopeForm' => $scopeForm->createView(),
        ]);
    }

    /**
     * @return Response
     */
    public function import(Request $request): RedirectResponse
    {
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
     * @return Response
     */
    public function export(Request $request): Response|RedirectResponse
    {
        $form = $this->createForm(ExportType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
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

    /**
     * @param int $id
     *
     * @return Response
     */
    public function deletePageType($id): RedirectResponse
    {
        $pageType = $this->em->getRepository($this->configurationManager->getEntityClass('page_type'))->find($id);

        if (!$pageType instanceof PageTypeInterface) {
            throw $this->createNotFoundException(
                sprintf('Entity %s with ID %s not found', $this->configurationManager->getEntityClass('page_type'), $id)
            );
        }

        $this->em->remove($pageType);
        $this->em->flush();

        return $this->redirectToRoute('sherlockode_acb_tools_index');
    }
}
