<?php
namespace meteor\exceptions;

use common\exceptions\EndpointExecutionException;

class InvalidGroupException extends EndpointExecutionException
{
    public function __construct($error, $data = [])
    {
        parent::__construct($error, $data);
    }
}