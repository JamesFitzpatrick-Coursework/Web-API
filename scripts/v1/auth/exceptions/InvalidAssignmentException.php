<?php
namespace meteor\exceptions;

use common\data\Token;
use common\exceptions\EndpointExecutionException;

class InvalidAssignmentException extends EndpointExecutionException
{
    public function __construct($error = "Could not find assignment with id provided.", Token $id = null)
    {
        parent::__construct($error, $id != null ? ["id" => $id->toString()] : []);
    }
}