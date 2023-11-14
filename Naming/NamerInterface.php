<?php

namespace Sherlockode\AdvancedContentBundle\Naming;

use Symfony\Component\HttpFoundation\File\File;

interface NamerInterface
{
    /**
     * @param File $file
     *
     * @return string
     */
    public function getFilename(File $file): string;
}
