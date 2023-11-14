<?php

namespace Sherlockode\AdvancedContentBundle\Naming;

use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ClientOriginalNamer implements NamerInterface
{
    /**
     * @param File $file
     *
     * @return string
     */
    public function getFilename(File $file): string
    {
        $fileName = $file->getFilename();

        if ($file instanceof UploadedFile) {
            $fileName = $file->getClientOriginalName();
        }

        return $fileName;
    }
}
