<?php

declare(strict_types=1);

namespace Sherlockode\AdvancedContentBundle\Naming;

use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class UniqueNamer implements NamerInterface
{
    public function getFilename(File $file): string
    {
        $extension = $file->getExtension();
        $fileName = $file->getFilename();

        if ($file instanceof UploadedFile) {
            $extension = $file->getClientOriginalExtension();
            $fileName = $file->getClientOriginalName();
        }

        $fileName = str_replace('.' . $extension, '', $fileName);

        return sprintf('%s_%s.%s', $fileName, md5(uniqid()), $extension);
    }
}
