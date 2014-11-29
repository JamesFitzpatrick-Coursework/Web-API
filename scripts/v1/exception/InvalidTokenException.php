<?php

class InvalidTokenException extends EndpointExecutionException
{
    public function __construct($error)
    {
        parent::__construct($error);
    }
}