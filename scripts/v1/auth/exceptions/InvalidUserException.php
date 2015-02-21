<?php
namespace meteor\exceptions;

use common\exceptions\EndpointExecutionException;

class InvalidUserException extends EndpointExecutionException
{
    public function __construct($error, $payload = [])
    {
        parent::__construct($error, $payload);
    }
}