<?php

class ValidationFailedException extends EndpointExecutionException
{

    public function __construct($error)
    {
        parent::__construct($error);
    }

    public function getErrorCode()
    {
        return HTTP_OK;
    }

} 