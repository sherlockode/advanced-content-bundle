<?php

namespace Sherlockode\AdvancedContentBundle\Twig\Extension;

use Doctrine\ORM\EntityManager;
use Sherlockode\AdvancedContentBundle\Manager\FieldManager;
use Sherlockode\AdvancedContentBundle\Manager\UrlBuilderManager;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class ContentExtension extends AbstractExtension
{
    /**
     * @var FieldManager
    */
    private $fieldManager;

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
     * @param FieldManager      $fieldManager
     * @param Environment       $twig
     * @param EntityManager     $em
     * @param UrlBuilderManager $urlBuilderManager
     * @param string            $baseFormTheme
     */
    public function __construct(
        FieldManager $fieldManager,
        Environment $twig,
        EntityManager $em,
        UrlBuilderManager $urlBuilderManager,
        $baseFormTheme
    ) {
        $this->fieldManager = $fieldManager;
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
            new TwigFunction('acb_field_raw_value', [$this, 'getFieldRawValue']),
            new TwigFunction('acb_base_form_theme', [$this, 'getBaseFormTheme']),
            new TwigFunction('acb_get_file_url', [$this, 'getFileUrl']),
            new TwigFunction('acb_get_full_url', [$this, 'getFullUrl']),
        ];
    }

    public function renderElement(array $element)
    {
        $field = $this->fieldManager->getFieldTypeByCode($element['fieldType']);

        $raw = $this->getFieldRawValue($element);
        if (is_array($raw)) {
            $params = $raw;
        } else {
            $params = ['value' => $raw];
        }

        return $this->twig->render($field->getFrontTemplate(), $params);
    }

    /**
     * @param array $element
     *
     * @return string
     */
    public function renderElementPreview(array $element)
    {
        $field = $this->fieldManager->getFieldTypeByCode($element['fieldType']);

        $raw = $this->getFieldRawValue($element);
        if (is_array($raw)) {
            $params = $raw;
        } else {
            $params = ['value' => $raw];
        }

        $template = $field->getPreviewTemplate();
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
     * Get element raw value
     *
     * @param array $element
     *
     * @return mixed
     */
    public function getFieldRawValue(array $element)
    {
        return $this->fieldManager->getFieldTypeByCode($element['fieldType'])->getRawValue($element['value'] ?? null);
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
