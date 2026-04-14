<?php

declare(strict_types=1);

namespace Sherlockode\AdvancedContentBundle\Twig\Extension;

use Doctrine\ORM\EntityManager;
use Sherlockode\AdvancedContentBundle\Manager\ElementManager;
use Sherlockode\AdvancedContentBundle\Manager\UrlBuilderManager;
use Sherlockode\AdvancedContentBundle\Model\ContentInterface;
use Sherlockode\AdvancedContentBundle\Model\VersionInterface;
use Sherlockode\AdvancedContentBundle\Scope\ScopeHandlerInterface;
use Sherlockode\AdvancedContentBundle\User\UserProviderInterface;
use Symfony\Component\Form\FormView;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class ContentExtension extends AbstractExtension
{
    /**
     * @param string $baseFormTheme
     */
    public function __construct(
        private readonly ElementManager $elementManager,
        private readonly Environment $twig,
        private readonly EntityManager $em,
        private readonly UrlBuilderManager $urlBuilderManager,
        private readonly UserProviderInterface $userProvider,
        private readonly ScopeHandlerInterface $scopeHandler,
        private $baseFormTheme,
    ) {
    }

    public function getFunctions(): array
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
            new TwigFunction('acb_get_version_user_name', [$this, 'getVersionUserName']),
            new TwigFunction('acb_get_content_by_slug', [$this, 'getContentBySlug']),
            new TwigFunction('acb_get_col_size', [$this, 'getColSize']),
        ];
    }

    public function renderElement(array $elementData): string
    {
        $element = $this->elementManager->getElementByCode($elementData['elementType']);
        $params = $element->getRawData($elementData);

        return $this->twig->render($element->getFrontTemplate(), $params);
    }

    public function renderElementPreview(array $elementData, ?FormView $form = null): string
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

    public function getFileUrl(string $fileName): string
    {
        return $this->urlBuilderManager->getFileUrl($fileName);
    }

    public function getFullUrl(string $url): string
    {
        return $this->urlBuilderManager->getFullUrl($url);
    }

    public function getElementLabel(string $elementType): string
    {
        $element = $this->elementManager->getElementByCode($elementType);

        return $element->getFormFieldLabel();
    }

    public function getColumnClasses(array $config): array
    {
        $classes = [];
        $size = $config['size'] ?? 12;
        $classes[] = '-' === $size ? 'col' : 'col-'.$size;
        $offset = $config['offset'] ?? 0;
        if (!empty($offset)) {
            $classes[] = 'offset-'.$offset;
        }

        $devices = [
            'sm',
            'md',
            'lg',
            'xl',
        ];
        foreach ($devices as $device) {
            if (isset($config['size_'.$device])) {
                $classes[] = '-' === $size ? 'col' : 'col-'.$device.'-'.$config['size_'.$device];
            }

            if (isset($config['offset_'.$device])) {
                $classes[] = 'offset-'.$device.'-'.$config['offset_'.$device];
            }
        }

        return $classes;
    }

    public function getRowClasses(array $config): array
    {
        $classes = [];
        $classes[] = 'justify-content-'.($config['justify_content'] ?? 'start');
        if ($config['mobile_reverse_columns'] ?? false) {
            $classes[] = 'flex-row-reverse flex-md-row';
        }

        return $classes;
    }

    public function getElementAttributes(array $extra, string $defaultDisplay = 'block'): array
    {
        return [
            'classes' => implode(' ', $this->getElementClasses($extra, $defaultDisplay)),
            'id' => $extra['advanced']['id'] ?? null,
            'style' => implode(';', $this->getElementStyles($extra)),
        ];
    }

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

        if ([] !== $hideOn) {
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
                    if (null === $lastHidden || ($lastHidden + 1) !== $key) {
                        $classes[] = 'd-'.('xs' === $device ? '' : $device.'-').'none';
                    }

                    $lastHidden = $key;
                } else {
                    if ('xs' !== $device && (null === $lastDisplayed || ($lastDisplayed + 1) !== $key)) {
                        $classes[] = 'd-'.$device.'-'.$defaultDisplay;
                    }

                    $lastDisplayed = $key;
                }
            }
        }

        return $classes;
    }

    private function getElementStyles(array $extra): array
    {
        $design = $extra['design'] ?? [];
        $styles = [];

        foreach ($this->getPixelProperties() as $property) {
            if ($design[$property] ?? null) {
                $styles[] = str_replace('_', '-', $property).':'.$design[$property].'px';
            }
        }

        $colorProperties = ['border', 'background'];
        foreach ($colorProperties as $colorProperty) {
            $color = $this->getColorForProperty($design, $colorProperty);
            if (null !== $color) {
                $styles[] = $colorProperty.'-color:'.$color;
            }
        }

        $borderStyle = $design['border_style'] ?? 'none';
        if ('none' !== $borderStyle) {
            $styles[] = 'border-style:'.$borderStyle;
        }

        return $styles;
    }

    private function getColorForProperty(array $design, string $property): ?string
    {
        $selectColor = $design[$property.'_color_select'] ?? 'none';
        if ('none' === $selectColor) {
            return null;
        }

        if ('transparent' === $selectColor) {
            return 'transparent';
        }

        return $design[$property.'_color'] ?? null;
    }

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
     * @return array|mixed
     */
    public function getJsonForm(FormView $form)
    {
        // Looping on multiple choice type children will return an array of all available choices,
        // using the form value allows us to retrieve only the selected choices
        $useValueForSerialization = (
            isset($form->vars['choices'])
            && isset($form->vars['multiple'])
            && true === $form->vars['multiple']
        );

        if ($form->vars['compound'] && !$useValueForSerialization) {
            foreach ($form->children as $child) {
                $json[$child->vars['name']] = $this->getJsonForm($child);
            }

            return $json ?? [];
        }

        if ($useValueForSerialization || is_object($form->vars['data'])) {
            return $form->vars['value'];
        }

        return $form->vars['data'];
    }

    public function getVersionUserName(VersionInterface $version): string
    {
        return $this->userProvider->getUserName($version->getUserId());
    }

    public function getContentBySlug(string $slug): ?ContentInterface
    {
        return $this->scopeHandler->getEntityForCurrentScope('content', ['slug' => $slug]);
    }

    public function getColSize(array $config): string
    {
        $colSize = $this->cleanColSize($config['size']);
        $colOffset = min(11, max(0, $config['offset'] ?? 0));

        $devices = ['sm', 'md', 'lg', 'xl'];

        foreach ($devices as $device) {
            if (isset($config['size_'.$device])) {
                $colSize = $this->cleanColSize($config['size_'.$device]);
            }

            if (isset($config['offset_'.$device])) {
                $colOffset = $config['offset_'.$device];
            }
        }

        if (!is_numeric($colSize)) {
            return $colSize;
        }

        return (string) ($colSize + $colOffset);
    }

    private function cleanColSize(int|string $value): string
    {
        if ('auto' === $value || '-' === $value) {
            return str_replace('-', '', $value);
        }

        return (string) min(12, max(1, is_numeric($value) ? $value : 12));
    }
}
