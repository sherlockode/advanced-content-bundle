<?php

namespace Sherlockode\AdvancedContentBundle\Import;

class ImportResult
{
    public const FAILURE = 0;
    public const SUCCESS = 1;
    public const UNKNOWN = 2;

    /**
     * @var int
     */
    private $status;

    /**
     * @var array
     */
    private $messages = [];

    public function __construct()
    {
        $this->status = self::UNKNOWN;
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
    public function isSuccess()
    {
        return self::SUCCESS == $this->status;
    }

    /**
     * @return $this
     */
    public function success()
    {
        $this->status = self::SUCCESS;

        return $this;
    }

    /**
     * @return $this
     */
    public function failure()
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
    public function addMessage($message)
    {
        $this->messages[] = $message;

        return $this;
    }
}
