<?php
namespace meteor\exceptions;

use common\exceptions\EndpointExecutionException;

class DatabaseException extends EndpointExecutionException
{
    public function __construct($error, $query = "", $data = [])
    {
        if (DEBUG && $query != "") {
            $data["query"] = $query;
        }
        parent::__construct($error, $data);
    }
}