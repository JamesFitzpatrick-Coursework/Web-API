<?php

class InvalidUserException extends EndpointExecutionException
{
    public function __construct($error)
    {
        parent::__construct($error);
    }
}