<?php

namespace Sherlockode\AdvancedContentBundle\Twig\Extension;

use Doctrine\ORM\EntityManager;
use Sherlockode\AdvancedContentBundle\Manager\ElementManager;
use Sherlockode\AdvancedContentBundle\Manager\UrlBuilderManager;
use Symfony\Component\Form\FormView;
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
            new TwigFunction('acb_get_element_label', [$this, 'getElementLabel']),
            new TwigFunction('acb_get_column_classes', [$this, 'getColumnClasses']),
            new TwigFunction('acb_get_row_classes', [$this, 'getRowClasses']),
            new TwigFunction('acb_get_element_attributes', [$this, 'getElementAttributes']),
            new TwigFunction('acb_get_json_form', [$this, 'getJsonForm']),
        ];
    }

    public function renderElement(array $elementData)
    {
        $element = $this->elementManager->getElementByCode($elementData['elementType']);
        $params = $element->getRawData($elementData);

        return $this->twig->render($element->getFrontTemplate(), $params);
    }

    /**
     * @param array         $elementData
     * @param FormView|null $form
     *
     * @return string
     */
    public function renderElementPreview(array $elementData, FormView $form = null): string
    {
        $element = $this->elementManager->getElementByCode($elementData['elementType']);

        $params = $element->getRawData($elementData);
        $template = $element->getPreviewTemplate();
        if (!$this->twig->getLoader()->exists($template)) {
            $template = '@SherlockodeAdvancedContent/Field/preview/no_preview.html.twig';
        }

        return $this->twig->render($template, array_merge($params, ['form' => $form]));
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

    /**
     * @param string $elementType
     *
     * @return string
     */
    public function getElementLabel(string $elementType): string
    {
        $element = $this->elementManager->getElementByCode($elementType);

        return $element->getFormFieldLabel();
    }

    /**
     * @param array $config
     *
     * @return array
     */
    public function getColumnClasses(array $config): array
    {
        $classes = [];
        $size = $config['size'] ?? 12;
        $classes[] = 'col-' . $size;
        $offset = $config['offset'] ?? 0;
        if (!empty($offset)) {
            $classes[] = 'offset-' . $offset;
        }

        $devices = [
            'sm',
            'md',
            'lg',
            'xl',
        ];
        foreach ($devices as $device) {
            if (isset($config['size_' . $device])) {
                $classes[] = 'col-' . $device . '-' . $config['size_' . $device];
            }
            if (isset($config['offset_' . $device])) {
                $classes[] = 'offset-' . $device . '-' . $config['offset_' . $device];
            }
        }

        return $classes;
    }

    /**
     * @param array $config
     *
     * @return array
     */
    public function getRowClasses(array $config): array
    {
        $classes = [];
        $classes[] = 'justify-content-' . ($config['justify_content'] ?? 'start');
        if ($config['mobile_reverse_columns'] ?? false) {
            $classes[] = 'flex-row-reverse flex-md-row';
        }

        return $classes;
    }

    /**
     * @param array  $extra
     * @param string $defaultDisplay
     *
     * @return array
     */
    public function getElementAttributes(array $extra, string $defaultDisplay = 'block'): array
    {
        return [
            'classes' => implode(' ', $this->getElementClasses($extra, $defaultDisplay)),
            'id' => $extra['advanced']['id'] ?? null,
            'style' => implode(';', $this->getElementStyles($extra)),
        ];
    }

    /**
     * @param array  $extra
     * @param string $defaultDisplay
     *
     * @return array
     */
    private function getElementClasses(array $extra, string $defaultDisplay = 'block'): array
    {
        $classes = [];

        $advanced = $extra['advanced'] ?? [];
        if ($advanced['class'] ?? '') {
            $classes[] = $advanced['class'];
        }

        $hideOn = $advanced['hide_on'] ?? [];
        if (!is_array($hideOn)) {
            $hideOn = [$hideOn];
        }
        if (count($hideOn) > 0) {
            $devices = [
                'xs',
                'sm',
                'md',
                'lg',
                'xl',
            ];
            $lastDisplayed = null;
            $lastHidden = null;

            foreach ($devices as $key => $device) {
                if (in_array($device, $hideOn)) {
                    if ($lastHidden === null || ($lastHidden + 1) !== $key) {
                        $classes[] = 'd-' . ($device === 'xs' ? '' : $device . '-') . 'none';
                    }
                    $lastHidden = $key;
                } else {
                    if ($device !== 'xs' && ($lastDisplayed === null || ($lastDisplayed + 1) !== $key)) {
                        $classes[] = 'd-' . $device . '-' . $defaultDisplay;
                    }
                    $lastDisplayed = $key;
                }
            }
        }

        return $classes;
    }

    /**
     * @param array $extra
     *
     * @return array
     */
    private function getElementStyles(array $extra): array
    {
        $design = $extra['design'] ?? [];
        $styles = [];

        foreach ($this->getPixelProperties() as $property) {
            if ($design[$property] ?? null) {
                $styles[] = str_replace('_', '-', $property) . ':' . $design[$property] . 'px';
            }
        }

        $colorProperties = ['border', 'background'];
        foreach ($colorProperties as $colorProperty) {
            $color = $this->getColorForProperty($design, $colorProperty);
            if ($color !== null) {
                $styles[] = $colorProperty . '-color:' . $color;
            }
        }

        $borderStyle = $design['border_style'] ?? 'none';
        if ($borderStyle !== 'none') {
            $styles[] = 'border-style:' . $borderStyle;
        }

        return $styles;
    }

    /**
     * @param array  $design
     * @param string $property
     *
     * @return string|null
     */
    private function getColorForProperty(array $design, string $property): ?string
    {
        $selectColor = $design[$property . '_color_select'] ?? 'none';
        if ($selectColor === 'none') {
            return null;
        }
        if ($selectColor === 'transparent') {
            return 'transparent';
        }

        return $design[$property . '_color'] ?? null;
    }

    /**
     * @return array
     */
    private function getPixelProperties(): array
    {
        $directions = ['top', 'right', 'bottom', 'left'];
        $properties = ['margin_%s', 'border_%s_width', 'padding_%s'];
        $pixelProperties = [];
        foreach ($properties as $property) {
            foreach ($directions as $direction) {
                $pixelProperties[] = sprintf($property, $direction);
            }
        }
        $pixelProperties = array_merge($pixelProperties, [
            'border_top_left_radius',
            'border_top_right_radius',
            'border_bottom_right_radius',
            'border_bottom_left_radius',
        ]);

        return $pixelProperties;
    }

    /**
     * @param FormView $form
     *
     * @return array|mixed
     */
    public function getJsonForm(FormView $form)
    {
        $json = [];
        if ($form->vars['compound'] && !in_array('acb_advanced_hide_on', $form->vars['block_prefixes'])) {
            foreach ($form->children as $child) {
                $json[$child->vars['name']] = $this->getJsonForm($child);
            }
        } else {
            return in_array('acb_advanced_hide_on', $form->vars['block_prefixes']) ? $form->vars['value'] : $form->vars['data'];
        }

        return $json;
    }
}
