<?php
namespace meteor\exceptions;

class InvalidClientException extends EndpointExecutionException {

    public function __construct()
    {
        parent::__construct("Missing client id header");
    }
} 