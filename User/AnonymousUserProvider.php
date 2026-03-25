<?php

namespace Sherlockode\AdvancedContentBundle\User;

use Symfony\Contracts\Translation\TranslatorInterface;

class AnonymousUserProvider implements UserProviderInterface
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
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
