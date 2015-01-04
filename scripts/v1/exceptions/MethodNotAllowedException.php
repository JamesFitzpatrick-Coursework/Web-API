<?php
namespace meteor\exceptions;

use meteor\core\HTTP;

class MethodNotAllowedException extends EndpointExecutionException
{
    public function __construct($method)
    {
        parent::__construct("Method not allowed", array("method" => $method));
    }

    public function getErrorCode()
    {
        return HTTP::METHOD_NOT_ALLOWED;
    }
}