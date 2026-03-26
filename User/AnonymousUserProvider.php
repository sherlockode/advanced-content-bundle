<?php

namespace Sherlockode\AdvancedContentBundle\User;

use Symfony\Contracts\Translation\TranslatorInterface;

class AnonymousUserProvider implements UserProviderInterface
{
    public function __construct(private readonly TranslatorInterface $translator)
    {
    }

    public function getUserId(): ?int
    {
        return null;
    }

    public function getUserName(?int $userId): string
    {
        return $this->translator->trans('version.user.anonymous', [], 'AdvancedContentBundle');
    }
}
