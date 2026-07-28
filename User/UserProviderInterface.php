<?php

declare(strict_types=1);

namespace Sherlockode\AdvancedContentBundle\User;

interface UserProviderInterface
{
    public function getUserId(): ?int;

    public function getUserName(?int $userId): string;
}
