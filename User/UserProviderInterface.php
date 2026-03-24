<?php

namespace Sherlockode\AdvancedContentBundle\User;

interface UserProviderInterface
{
    public function getUserId(): ?int;

    public function getUserName(?int $userId): string;
}
