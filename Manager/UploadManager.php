<?php

namespace Sherlockode\AdvancedContentBundle\Manager;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class UploadManager
{
    /**
     * @var string
     */
    private $targetDir;

    /**
     * @var string
     */
    private $webPath;

    public function __construct($targetDir, $webPath)
    {
        $this->targetDir = $targetDir;
        $this->webPath = $webPath;
    }

    /**
     * Upload file on server
     *
     * @param UploadedFile|null $file
     *
     * @return string
     */
    public function upload(UploadedFile $file = null)
    {
        if ($file === null) {
            return '';
        }

        $fileName = $this->getFileName($file);
        $file->move($this->getTargetDir(), $fileName);

        return $fileName;
    }

    /**
     * Remove file
     *
     * @param string $fileName
     */
    public function remove($fileName)
    {
        $fileName = $this->getTargetDir() . DIRECTORY_SEPARATOR . $fileName;

        if (!file_exists($fileName)) {
            return;
        }

        unlink($fileName);
    }

    /**
     * Get file name
     *
     * @param UploadedFile $file
     *
     * @return string
     */
    private function getFileName(UploadedFile $file)
    {
        $extension = $file->getClientOriginalExtension();
        $fileName = str_replace('.' . $extension, '', $file->getClientOriginalName());

        $fileName = $fileName . '_' . md5(uniqid()) . '.' . $extension;

        return $fileName;
    }

    /**
     * @param string $src
     *
     * @return bool
     */
    public function isFileUploaded($src)
    {
        if (empty($src)) {
            return false;
        }

        $fileName = $this->getTargetDir() . DIRECTORY_SEPARATOR . $src;

        return file_exists($fileName);
    }

    /**
     * Get target directory
     *
     * @return string
     */
    public function getTargetDir()
    {
        return $this->targetDir;
    }

    public function getWebPath()
    {
        return $this->webPath;
    }
}
