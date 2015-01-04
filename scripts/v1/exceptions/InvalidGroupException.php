<?php
namespace meteor\exceptions;

class InvalidGroupException extends EndpointExecutionException
{
    public function __construct($error)
    {
        parent::__construct($error);
    }
}