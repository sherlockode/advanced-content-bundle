<?php

declare(strict_types=1);

namespace Sherlockode\AdvancedContentBundle\Manager;

use Sherlockode\AdvancedContentBundle\Naming\NamerInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class UploadManager
{
    /**
     * @param string $targetDir
     * @param string $webPath
     */
    public function __construct(
        private readonly NamerInterface $fileNamer,
        private $targetDir,
        private $webPath,
    ) {
    }

    /**
     * Upload file on server.
     *
     * @return string
     */
    public function upload(?UploadedFile $file = null, ?string $fileName = null)
    {
        if (null === $file) {
            return '';
        }

        $fileName ??= $this->getFileName($file);
        $file->move($this->getTargetDir(), $fileName);

        return $fileName;
    }

    /**
     * Copy file into acb files directory.
     *
     * @return string
     */
    public function copy(File $file)
    {
        $fileName = $this->getFileName($file);
        if (!$file->isReadable()) {
            throw new \Exception(sprintf('Source file %s does not exist', $file->getRealPath()));
        }

        if (!is_writable($this->getTargetDir())) {
            throw new \Exception(sprintf('Target directory %s is not writeable', $this->getTargetDir()));
        }

        copy($file->getRealPath(), $this->getTargetDir().DIRECTORY_SEPARATOR.$fileName);

        return $fileName;
    }

    /**
     * Remove file.
     *
     * @param string $fileName
     */
    public function remove($fileName): void
    {
        $fileName = $this->getTargetDir().DIRECTORY_SEPARATOR.$fileName;

        if (!file_exists($fileName)) {
            return;
        }

        unlink($fileName);
    }

    /**
     * Get file name.
     *
     * @param UploadedFile|File $file
     */
    public function getFileName(File $file): string
    {
        return $this->fileNamer->getFilename($file);
    }

    /**
     * @return bool
     */
    public function isFileUploaded(?string $src)
    {
        if (empty($src)) {
            return false;
        }

        $fileName = $this->getTargetDir().DIRECTORY_SEPARATOR.$src;

        return file_exists($fileName);
    }

    /**
     * Get target directory.
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
