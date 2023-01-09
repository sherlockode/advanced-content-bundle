<?php

namespace Sherlockode\AdvancedContentBundle\Twig\Extension;

use Doctrine\ORM\EntityManager;
use Sherlockode\AdvancedContentBundle\Manager\FieldManager;
use Sherlockode\AdvancedContentBundle\Manager\UrlBuilderManager;
use Sherlockode\AdvancedContentBundle\Model\FieldValueInterface;
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
            new TwigFunction('acb_render_field', [$this, 'renderFieldValue'], ['is_safe' => ['html']]),
            new TwigFunction('acb_field_preview', [$this, 'renderFieldPreview'], ['is_safe' => ['html']]),
            new TwigFunction('acb_find_entity', [$this, 'findEntity']),
            new TwigFunction('acb_field_raw_value', [$this, 'getFieldRawValue']),
            new TwigFunction('acb_base_form_theme', [$this, 'getBaseFormTheme']),
            new TwigFunction('acb_get_file_url', [$this, 'getFileUrl']),
            new TwigFunction('acb_get_full_url', [$this, 'getFullUrl']),
        ];
    }

    public function renderFieldValue(FieldValueInterface $fieldValue)
    {
        $field = $this->fieldManager->getFieldTypeByCode($fieldValue->getFieldType());

        $raw = $this->getFieldRawValue($fieldValue);
        if (is_array($raw)) {
            $params = $raw;
        } else {
            $params = ['value' => $raw];
        }

        return $this->twig->render($field->getFrontTemplate(), $params);
    }

    /**
     * @param FieldValueInterface $fieldValue
     *
     * @return string
     */
    public function renderFieldPreview(FieldValueInterface $fieldValue)
    {
        $field = $this->fieldManager->getFieldTypeByCode($fieldValue->getFieldType());

        $raw = $this->getFieldRawValue($fieldValue);
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
     * Get FieldValue raw value
     *
     * @param FieldValueInterface $fieldValue
     *
     * @return mixed
     */
    public function getFieldRawValue(FieldValueInterface $fieldValue)
    {
        return $this->fieldManager->getFieldTypeByCode($fieldValue->getFieldType())->getRawValue($fieldValue);
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
