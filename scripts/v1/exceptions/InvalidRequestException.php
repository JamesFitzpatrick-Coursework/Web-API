<?php
namespace meteor\exceptions;

class InvalidRequestException extends EndpointExecutionException
{
    function __construct($missing)
    {
        parent::__construct("Missing parameters in the request", array ("missing" => $missing));
    }
}