<?php
namespace common\exceptions;

class InvalidTokenException extends EndpointExecutionException
{
    public function __construct($error, $payload = array())
    {
        parent::__construct($error, $payload);
    }
}