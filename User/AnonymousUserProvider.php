<?php

namespace Sherlockode\AdvancedContentBundle\User;

use Symfony\Contracts\Translation\TranslatorInterface;

class AnonymousUserProvider implements UserProviderInterface
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @return int|null
     */
    public function getUserId(): ?int
    {
        return null;
    }

    /**
     * @param int|null $userId
     *
     * @return string
     */
    public function getUserName(?int $userId): string
    {
        return $this->translator->trans('version.user.anonymous', [], 'AdvancedContentBundle');
    }
}
