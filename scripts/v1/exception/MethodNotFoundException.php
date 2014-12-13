<?php

class MethodNotFoundException extends EndpointExecutionException
{
    public function __construct($request)
    {
        parent::__construct("Method not found", array("request" => $request));
    }

    public function getErrorCode()
    {
        return HTTP_NOT_FOUND;
    }
}