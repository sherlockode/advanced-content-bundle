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
use Twig\Attribute\AsTwigFunction;
use Twig\Environment;

class ContentExtension
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

    #[AsTwigFunction(name: 'acb_render_element', isSafe: ['html'])]
    public function renderElement(array $elementData): string
    {
        $element = $this->elementManager->getElementByCode($elementData['elementType']);
        $params = $element->getRawData($elementData);

        return $this->twig->render($element->getFrontTemplate(), $params);
    }

    #[AsTwigFunction(name: 'acb_element_preview', isSafe: ['html'])]
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

    #[AsTwigFunction(name: 'acb_find_entity')]
    public function findEntity($identifier, $class)
    {
        return $this->em->getRepository($class)->find($identifier);
    }

    /**
     * @return string
     */
    #[AsTwigFunction(name: 'acb_base_form_theme')]
    public function getBaseFormTheme()
    {
        return $this->baseFormTheme;
    }

    #[AsTwigFunction(name: 'acb_get_file_url')]
    public function getFileUrl(string $fileName): string
    {
        return $this->urlBuilderManager->getFileUrl($fileName);
    }

    #[AsTwigFunction(name: 'acb_get_full_url')]
    public function getFullUrl(string $url): string
    {
        return $this->urlBuilderManager->getFullUrl($url);
    }

    #[AsTwigFunction(name: 'acb_get_element_label')]
    public function getElementLabel(string $elementType): string
    {
        $element = $this->elementManager->getElementByCode($elementType);

        return $element->getFormFieldLabel();
    }

    #[AsTwigFunction(name: 'acb_get_column_classes')]
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

    #[AsTwigFunction(name: 'acb_get_row_classes')]
    public function getRowClasses(array $config): array
    {
        $classes = [];
        $classes[] = 'justify-content-'.($config['justify_content'] ?? 'start');
        if ($config['mobile_reverse_columns'] ?? false) {
            $classes[] = 'flex-row-reverse flex-md-row';
        }

        return $classes;
    }

    #[AsTwigFunction(name: 'acb_get_element_attributes')]
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
    #[AsTwigFunction(name: 'acb_get_json_form')]
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

    #[AsTwigFunction(name: 'acb_get_version_user_name')]
    public function getVersionUserName(VersionInterface $version): string
    {
        return $this->userProvider->getUserName($version->getUserId());
    }

    #[AsTwigFunction(name: 'acb_get_content_by_slug')]
    public function getContentBySlug(string $slug): ?ContentInterface
    {
        return $this->scopeHandler->getEntityForCurrentScope('content', ['slug' => $slug]);
    }

    #[AsTwigFunction(name: 'acb_get_col_size')]
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

        return $colSize + $colOffset;
    }

    private function cleanColSize(string $value): string
    {
        if ('auto' === $value || '-' === $value) {
            return str_replace('-', '', $value);
        }

        return min(12, max(1, is_numeric($value) ? $value : 12));
    }
}
