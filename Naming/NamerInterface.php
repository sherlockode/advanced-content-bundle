<?php

declare(strict_types=1);

namespace Sherlockode\AdvancedContentBundle\Naming;

use Symfony\Component\HttpFoundation\File\File;

interface NamerInterface
{
    public function getFilename(File $file): string;
}
