<?php
namespace meteor\exceptions;

use common\exceptions\EndpointExecutionException;
use meteor\data\profiles\AssessmentProfile;

class InvalidAssignmentException extends EndpointExecutionException
{
    public function __construct(AssessmentProfile $profile, $error = "Could not find assessment with id provided.")
    {
        parent::__construct($error, $profile != null ? ["id" => $profile->getAssessmentId()->toString()] : []);
    }
}