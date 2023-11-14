<?php

namespace Sherlockode\AdvancedContentBundle\Manager;

use Sherlockode\AdvancedContentBundle\Naming\NamerInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class UploadManager
{
    /**
     * @var NamerInterface
     */
    private $fileNamer;

    /**
     * @var string
     */
    private $targetDir;

    /**
     * @var string
     */
    private $webPath;

    public function __construct(NamerInterface $fileNamer, $targetDir, $webPath)
    {
        $this->fileNamer = $fileNamer;
        $this->targetDir = $targetDir;
        $this->webPath = $webPath;
    }

    /**
     * Upload file on server
     *
     * @param UploadedFile|null $file
     * @param string|null       $fileName
     *
     * @return string
     */
    public function upload(UploadedFile $file = null,  ?string $fileName = null)
    {
        if ($file === null) {
            return '';
        }

        $fileName =  $fileName ?? $this->getFileName($file);
        $file->move($this->getTargetDir(), $fileName);

        return $fileName;
    }

    /**
     * Copy file into acb files directory
     *
     * @param File $file
     *
     * @return string
     */
    public function copy(File $file)
    {
        $fileName = $this->getFileName($file);
        if (!$file->isReadable()) {
            throw new \Exception(sprintf('Source file %s does not exist', $file->getRealPath()));
        }
        if (!is_writeable($this->getTargetDir())) {
            throw new \Exception(sprintf('Target directory %s is not writeable', $this->getTargetDir()));
        }
        copy($file->getRealPath(), $this->getTargetDir() . DIRECTORY_SEPARATOR . $fileName);

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
     * @param UploadedFile|File $file
     *
     * @return string
     */
    public function getFileName(File $file)
    {
        return $this->fileNamer->getFilename($file);
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
