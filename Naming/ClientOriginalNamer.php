<?php

declare(strict_types=1);

namespace Sherlockode\AdvancedContentBundle\Naming;

use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ClientOriginalNamer implements NamerInterface
{
    public function getFilename(File $file): string
    {
        if ($file instanceof UploadedFile) {
            return $file->getClientOriginalName();
        }
        return $file->getFilename();
    }
}
