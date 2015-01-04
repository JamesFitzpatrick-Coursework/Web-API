<?php
namespace meteor\exceptions;

class DatabaseException extends EndpointExecutionException
{
    public function __construct($error, $query = "")
    {
        parent::__construct($error, DEBUG && $query != "" ? array("query" => $query) : array());
    }
}