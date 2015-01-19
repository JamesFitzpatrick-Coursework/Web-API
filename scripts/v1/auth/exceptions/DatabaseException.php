<?php
namespace meteor\exceptions;

use common\exceptions\EndpointExecutionException;

class DatabaseException extends EndpointExecutionException
{
    public function __construct($error, $query = "")
    {
        parent::__construct($error, DEBUG && $query != "" ? array("query" => $query) : array());
    }
}