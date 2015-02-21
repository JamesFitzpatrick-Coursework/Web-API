<?php
namespace meteor\exceptions;

use common\exceptions\EndpointExecutionException;
use meteor\data\profiles\AssessmentProfile;

class InvalidAssessmentException extends EndpointExecutionException
{
    public function __construct(AssessmentProfile $profile)
    {
        parent::__construct("Could not find assessment with id provided.", ["id" => $profile->getAssessmentId()->toString()]);
    }
}