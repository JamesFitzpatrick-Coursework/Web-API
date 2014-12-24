<?php

class DatabaseException extends EndpointExecutionException
{
    public function __construct($error, $query = "")
    {
        parent::__construct($error, DEBUG ? array("query" => $query) : array());
    }
}