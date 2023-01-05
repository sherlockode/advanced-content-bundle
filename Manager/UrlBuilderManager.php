<?php

namespace Sherlockode\AdvancedContentBundle\Manager;

use Symfony\Component\Asset\Packages;
use Symfony\Component\HttpFoundation\RequestStack;

class UrlBuilderManager
{
    /**
     * @var UploadManager
     */
    private $uploadManager;

    /**
     * @var Packages
     */
    private $assetPackages;

    /**
     * @var RequestStack
     */
    private $requestStack;

    public function __construct(
        UploadManager $uploadManager,
        Packages $assetPackages,
        RequestStack $requestStack
    ) {
        $this->uploadManager = $uploadManager;
        $this->assetPackages = $assetPackages;
        $this->requestStack = $requestStack;
    }

    /**
     * @param string $fileName
     *
     * @return string
     */
    public function getFileUrl(string $fileName): string
    {
        if (!$fileName) {
            return '';
        }

        $filePath = $this->uploadManager->getTargetDir() . DIRECTORY_SEPARATOR . $fileName;
        if (!file_exists($filePath)) {
            return '';
        }

        return $this->assetPackages->getUrl($this->uploadManager->getWebPath() . '/' . $fileName);
    }

    /**
     * @param string $url
     *
     * @return string
     */
    public function getFullUrl(string $url): string
    {
        if (!$url) {
            return '';
        }

        if (substr($url, 0, 1) === '#') {
            return $url;
        }
        if (substr($url, 0, 4) === 'http') {
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

        return $mainRequest->getSchemeAndHttpHost() . '/' . ltrim($url, '/');
    }
}
