<?php

declare(strict_types=1);

namespace Sherlockode\AdvancedContentBundle\Manager;

use Symfony\Component\Asset\Packages;
use Symfony\Component\HttpFoundation\RequestStack;

class UrlBuilderManager
{
    public function __construct(
        private readonly UploadManager $uploadManager,
        private readonly Packages $assetPackages,
        private readonly RequestStack $requestStack,
    ) {
    }

    public function getFileUrl(string $fileName): string
    {
        if (!$fileName) {
            return '';
        }

        $filePath = $this->uploadManager->getTargetDir().DIRECTORY_SEPARATOR.$fileName;
        if (!file_exists($filePath)) {
            return '';
        }

        return $this->assetPackages->getUrl($this->uploadManager->getWebPath().'/'.$fileName);
    }

    public function getFullUrl(string $url): string
    {
        if (!$url) {
            return '';
        }

        if (str_starts_with($url, '#')) {
            return $url;
        }

        if (str_starts_with($url, 'http')) {
            return $url;
        }

        if (method_exists($this->requestStack, 'getMainRequest')) {
            // SF >= 5.3
            $mainRequest = $this->requestStack->getMainRequest();
        } else {
            // compat SF < 5.3
            $mainRequest = $this->requestStack->getMasterRequest();
        }

        if (!$mainRequest) {
            return $url;
        }

        return $mainRequest->getSchemeAndHttpHost().'/'.ltrim($url, '/');
    }
}
