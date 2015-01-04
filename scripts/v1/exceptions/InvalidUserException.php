<?php
namespace meteor\exceptions;

class InvalidUserException extends EndpointExecutionException
{
    public function __construct($error, $payload = array())
    {
        parent::__construct($error, $payload);
    }
}