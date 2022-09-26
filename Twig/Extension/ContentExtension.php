<?php

namespace Sherlockode\AdvancedContentBundle\Twig\Extension;

use Doctrine\ORM\EntityManager;
use Sherlockode\AdvancedContentBundle\Manager\FieldManager;
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
     * @var string
     */
    private $baseFormTheme;

    /**
     * @param FieldManager  $fieldManager
     * @param Environment   $twig
     * @param EntityManager $em
     * @param string        $baseFormTheme
     */
    public function __construct(FieldManager $fieldManager, Environment $twig, EntityManager $em, $baseFormTheme)
    {
        $this->fieldManager = $fieldManager;
        $this->twig = $twig;
        $this->em = $em;
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
}
