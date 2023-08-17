<?php

namespace Sherlockode\AdvancedContentBundle\Event;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Contracts\EventDispatcher\Event;

class AcbFilePreSubmitEvent extends Event
{
    public const NAME = 'acb_file.pre_submit';

    /**
     * @var UploadedFile
     */
    private $uploadedFile;

    /**
     * @var string
     */
    private $fileName;

    /**
     * @param UploadedFile $uploadedFile
     * @param string       $fileName
     */
    public function __construct(UploadedFile $uploadedFile, string $fileName)
    {
        $this->uploadedFile = $uploadedFile;
        $this->fileName = $fileName;
    }

    /**
     * @return UploadedFile
     */
    public function getUploadedFile(): UploadedFile
    {
        return $this->uploadedFile;
    }

    /**
     * @return string
     */
    public function getFileName(): string
    {
        return $this->fileName;
    }
}
