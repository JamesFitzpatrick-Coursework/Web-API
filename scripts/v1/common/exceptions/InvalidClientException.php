<?php
namespace common\exceptions;

class InvalidClientException extends EndpointExecutionException
{
    public function __construct()
    {
        parent::__construct("Missing client id header");
    }
} 