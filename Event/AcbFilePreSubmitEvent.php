<?php

declare(strict_types=1);

namespace Sherlockode\AdvancedContentBundle\Event;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Contracts\EventDispatcher\Event;

class AcbFilePreSubmitEvent extends Event
{
    public const NAME = 'acb_file.pre_submit';

    public function __construct(
        private readonly UploadedFile $uploadedFile,
        private readonly string $fileName,
    ) {
    }

    public function getUploadedFile(): UploadedFile
    {
        return $this->uploadedFile;
    }

    public function getFileName(): string
    {
        return $this->fileName;
    }
}
