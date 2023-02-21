<?php

namespace Sherlockode\AdvancedContentBundle\User;

interface UserProviderInterface
{
    /**
     * @return int|null
     */
    public function getUserId(): ?int;

    /**
     * @param int|null $userId
     *
     * @return string
     */
    public function getUserName(?int $userId): string;
}
