<?php
namespace meteor\exceptions;

use meteor\core\HTTP;

class AuthenticationException extends EndpointExecutionException {

    public function __construct($error, array $payload)
    {
        parent::__construct($error, $payload);
    }

    public function getErrorCode()
    {
        return HTTP::UNAUTHORIZED;
    }
}