<?php

namespace App\Support;

use RuntimeException;

class DomainConflictException extends RuntimeException
{
    private string $domainErrorCode;

    public function __construct(string $message, string $errorCode)
    {
        parent::__construct($message);
        $this->domainErrorCode = $errorCode;
    }

    public function errorCode(): string
    {
        return $this->domainErrorCode;
    }
}
