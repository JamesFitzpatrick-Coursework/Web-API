<?php

class DatabaseException extends EndpointExecutionException
{
    public function __construct($error, $query = "")
    {
        parent::__construct($error, array ("query" => $query));
    }
}