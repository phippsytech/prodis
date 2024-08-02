<?php

namespace NDISmate\CORE;

use Exception;

class UnauthorizedException extends Exception
{
    public function __construct($message = 'Authentication error occurred', $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
