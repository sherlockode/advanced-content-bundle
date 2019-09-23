<?php

namespace Sherlockode\AdvancedContentBundle\Import;

class ImportResult
{
    const FAILURE = 0;
    const SUCCESS = 1;
    const UNKNOWN = 2;

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
        return $this->status == self::SUCCESS;
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
