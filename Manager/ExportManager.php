<?php

declare(strict_types=1);

namespace Sherlockode\AdvancedContentBundle\Manager;

use Sherlockode\AdvancedContentBundle\Export\ContentExport;
use Sherlockode\AdvancedContentBundle\Export\PageExport;
use Sherlockode\AdvancedContentBundle\Model\ContentInterface;
use Sherlockode\AdvancedContentBundle\Model\PageInterface;
use Symfony\Component\Yaml\Yaml;

class ExportManager
{
    private array $filesData = [];

    public function __construct(
        private readonly PageExport $pageExport,
        private readonly ContentExport $contentExport
    ) {
        $this->pageExport->setContentExport($this->contentExport);
    }

    /**
     * @param array|PageInterface[] $pages
     */
    public function generatePagesData($pages): void
    {
        foreach ($pages as $page) {
            /** @var PageInterface $page */
            $data = $this->pageExport->exportData($page);
            $this->addToFilesData($data, 'page_' . $page->getPageIdentifier());
        }
    }

    /**
     * @param array|ContentInterface[] $contents
     */
    public function generateContentsData($contents): void
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
    private function addToFilesData($data, string $filename): void
    {
        $data = Yaml::dump($data, 15);
        $this->filesData[$filename . '.yaml'] = $data;
    }

    /**
     * @param string $directory
     * @param bool   $useDatePrefix
     */
    public function generateFiles(string $directory, $useDatePrefix = true): void
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
    public function generateZipFile(): string
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
