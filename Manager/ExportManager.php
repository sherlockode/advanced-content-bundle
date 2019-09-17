<?php

namespace Sherlockode\AdvancedContentBundle\Manager;

use Sherlockode\AdvancedContentBundle\Export\ContentExport;
use Sherlockode\AdvancedContentBundle\Export\ContentTypeExport;
use Sherlockode\AdvancedContentBundle\Export\PageExport;
use Sherlockode\AdvancedContentBundle\Model\ContentInterface;
use Sherlockode\AdvancedContentBundle\Model\ContentTypeInterface;
use Sherlockode\AdvancedContentBundle\Model\PageInterface;
use Symfony\Component\Yaml\Yaml;

class ExportManager
{
    /**
     * @var ContentTypeExport
     */
    private $contentTypeExport;

    /**
     * @var PageExport
     */
    private $pageExport;

    /**
     * @var ContentExport
     */
    private $contentExport;

    /**
     * @var array
     */
    private $filesData = [];

    /**
     * @param ContentTypeExport $contentTypeExport
     * @param PageExport        $pageExport
     * @param ContentExport     $contentExport
     */
    public function __construct(
        ContentTypeExport $contentTypeExport,
        PageExport $pageExport,
        ContentExport $contentExport
    ) {
        $this->contentTypeExport = $contentTypeExport;
        $this->pageExport = $pageExport;
        $this->contentExport = $contentExport;
        $this->pageExport->setContentExport($this->contentExport);
    }

    /**
     * @param array|ContentTypeInterface[] $contentTypes
     */
    public function generateContentTypesData($contentTypes)
    {
        foreach ($contentTypes as $contentType) {
            /** @var ContentTypeInterface $contentType */
            $data = $this->contentTypeExport->exportData($contentType);
            $this->addToFilesData($data, 'content-type_' . $contentType->getSlug());
        }
    }

    /**
     * @param array|PageInterface[] $pages
     */
    public function generatePagesData($pages)
    {
        foreach ($pages as $page) {
            /** @var PageInterface $page */
            $data = $this->pageExport->exportData($page);
            $this->addToFilesData($data, 'page_' . $page->getSlug());
        }
    }

    /**
     * @param array|ContentInterface[] $contents
     */
    public function generateContentsData($contents)
    {
        foreach ($contents as $content) {
            /** @var ContentInterface $content */
            $data = $this->contentExport->exportData($content);
            $this->addToFilesData($data, 'content_' . $content->getSlug());
        }
    }

    /**
     * @param array  $data
     * @param string $filename
     */
    private function addToFilesData($data, $filename)
    {
        $data = Yaml::dump($data, 15);
        $this->filesData[$filename . '.yaml'] = $data;
    }

    /**
     * @param string $directory
     * @param bool   $useDatePrefix
     */
    public function generateFiles($directory, $useDatePrefix = true)
    {
        $prefix = '';
        if ($useDatePrefix) {
            $prefix = date('Ymd-His_');
        }
        foreach ($this->filesData as $filename => $data) {
            file_put_contents($directory . $prefix . $filename, $data);
        }
    }

    /**
     * @return string
     */
    public function generateZipFile()
    {
        $tmpDir = '/tmp/acb_export_' . time() . '/';
        mkdir($tmpDir);
        $this->generateFiles($tmpDir, false);

        $zipFileName = '/tmp/acb_export_' . date('Ymd-His') . '.zip';
        $zip = new \ZipArchive();
        $zip->open($zipFileName, \ZipArchive::CREATE);
        $zip->addPattern('/.*/', $tmpDir, ['remove_all_path' => true]);
        $zip->close();

        foreach (glob($tmpDir . '*') as $file) {
            unlink($file);
        }
        rmdir($tmpDir);

        return $zipFileName;
    }

}
