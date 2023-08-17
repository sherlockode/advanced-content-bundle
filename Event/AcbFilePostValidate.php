<?php

namespace Sherlockode\AdvancedContentBundle\Event;

use Symfony\Contracts\EventDispatcher\Event;

class AcbFilePostValidate extends Event
{
    public const NAME = 'acb_file.post_validate';
}
