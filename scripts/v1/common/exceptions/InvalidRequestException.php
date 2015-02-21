<?php
namespace common\exceptions;

class InvalidRequestException extends EndpointExecutionException
{
    function __construct($missing)
    {
        parent::__construct("Missing parameters in the request", ["missing" => $missing]);
    }
}