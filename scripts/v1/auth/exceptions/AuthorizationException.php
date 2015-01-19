<?php
namespace meteor\exceptions;

use common\core\HTTP;
use common\exceptions\EndpointExecutionException;

class AuthorizationException extends EndpointExecutionException
{
    public function __construct($error, $payload = array ())
    {
        parent::__construct($error, $payload);
    }

    public function get_error_code()
    {
        return HTTP::UNAUTHORIZED;
    }
} 