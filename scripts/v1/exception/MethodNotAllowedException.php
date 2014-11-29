<?php

class MethodNotAllowedException extends EndpointExecutionException
{
    public function __construct($method)
    {
        parent::__construct("Method not allowed", array("method" => $method));
    }

    public function getErrorCode()
    {
        return HTTP_METHOD_NOT_ALLOWED;
    }
}