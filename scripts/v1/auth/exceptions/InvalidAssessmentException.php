<?php
namespace meteor\exceptions;

use common\data\Token;
use common\exceptions\EndpointExecutionException;

class InvalidAssessmentException extends EndpointExecutionException
{
    public function __construct(Token $profile)
    {
        parent::__construct("Could not find assessment with id provided.", ["id" => $profile->toString()]);
    }
}