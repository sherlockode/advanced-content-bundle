<?php

namespace Sherlockode\AdvancedContentBundle\Twig\Extension;

use Doctrine\ORM\EntityManager;
use Sherlockode\AdvancedContentBundle\Manager\ElementManager;
use Sherlockode\AdvancedContentBundle\Manager\UrlBuilderManager;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class ContentExtension extends AbstractExtension
{
    /**
     * @var ElementManager
    */
    private $elementManager;

    /**
     * @var Environment
     */
    private $twig;

    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var UrlBuilderManager
     */
    private $urlBuilderManager;

    /**
     * @var string
     */
    private $baseFormTheme;

    /**
     * @param ElementManager    $elementManager
     * @param Environment       $twig
     * @param EntityManager     $em
     * @param UrlBuilderManager $urlBuilderManager
     * @param string            $baseFormTheme
     */
    public function __construct(
        ElementManager $elementManager,
        Environment $twig,
        EntityManager $em,
        UrlBuilderManager $urlBuilderManager,
        $baseFormTheme
    ) {
        $this->elementManager = $elementManager;
        $this->twig = $twig;
        $this->em = $em;
        $this->urlBuilderManager = $urlBuilderManager;
        $this->baseFormTheme = $baseFormTheme;
    }

    /**
     * Add specific twig function
     *
     * @return TwigFunction[]
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('acb_render_element', [$this, 'renderElement'], ['is_safe' => ['html']]),
            new TwigFunction('acb_element_preview', [$this, 'renderElementPreview'], ['is_safe' => ['html']]),
            new TwigFunction('acb_find_entity', [$this, 'findEntity']),
            new TwigFunction('acb_base_form_theme', [$this, 'getBaseFormTheme']),
            new TwigFunction('acb_get_file_url', [$this, 'getFileUrl']),
            new TwigFunction('acb_get_full_url', [$this, 'getFullUrl']),
        ];
    }

    public function renderElement(array $elementData)
    {
        $element = $this->elementManager->getElementByCode($elementData['elementType']);
        $params = $element->getRawData($elementData);

        return $this->twig->render($element->getFrontTemplate(), $params);
    }

    /**
     * @param array $elementData
     *
     * @return string
     */
    public function renderElementPreview(array $elementData)
    {
        $element = $this->elementManager->getElementByCode($elementData['elementType']);

        $params = $element->getRawData($elementData);
        $template = $element->getPreviewTemplate();
        if (!$this->twig->getLoader()->exists($template)) {
            $template = '@SherlockodeAdvancedContent/Field/preview/no_preview.html.twig';
        }

        return $this->twig->render($template, $params);
    }


    public function findEntity($identifier, $class)
    {
        return $this->em->getRepository($class)->find($identifier);
    }

    /**
     * @return string
     */
    public function getBaseFormTheme()
    {
        return $this->baseFormTheme;
    }

    /**
     * @param string $fileName
     *
     * @return string
     */
    public function getFileUrl(string $fileName): string
    {
        return $this->urlBuilderManager->getFileUrl($fileName);
    }

    /**
     * @param string $url
     *
     * @return string
     */
    public function getFullUrl(string $url): string
    {
        return $this->urlBuilderManager->getFullUrl($url);
    }
}
