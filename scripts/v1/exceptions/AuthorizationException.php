<?php
namespace meteor\exceptions;

use meteor\core\HTTP;

class AuthorizationException extends EndpointExecutionException
{
    public function __construct($error, $payload = array ())
    {
        parent::__construct($error, $payload);
    }

    public function getErrorCode()
    {
        return HTTP::UNAUTHORIZED;
    }
} 