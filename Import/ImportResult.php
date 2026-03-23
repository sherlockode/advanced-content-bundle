<?php

declare(strict_types=1);

namespace Sherlockode\AdvancedContentBundle\Import;

class ImportResult
{
    const FAILURE = 0;

    const SUCCESS = 1;

    const UNKNOWN = 2;

    private int $status = self::UNKNOWN;

    /**
     * @var array
     */
    private $messages = [];

    public function __construct()
    {
    }

    /**
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return bool
     */
    public function isSuccess(): bool
    {
        return $this->status == self::SUCCESS;
    }

    /**
     * @return $this
     */
    public function success(): static
    {
        $this->status = self::SUCCESS;

        return $this;
    }

    /**
     * @return $this
     */
    public function failure(): static
    {
        $this->status = self::FAILURE;

        return $this;
    }

    /**
     * @return array
     */
    public function getMessages()
    {
        return $this->messages;
    }

    /**
     * @param string $message
     *
     * @return $this
     */
    public function addMessage($message): static
    {
        $this->messages[] = $message;

        return $this;
    }
}
