<?php
namespace meteor\endpoints\assessments;

use meteor\data\profiles\AssessmentProfile;
use meteor\database\Backend;
use meteor\database\backend\AssessmentBackend;
use meteor\endpoints\AuthenticatedEndpoint;

class AssessmentsListEndpoint extends AuthenticatedEndpoint
{
    public function handle($data)
    {
        $assessments = [];

        /** @var AssessmentProfile $assessment */
        foreach (AssessmentBackend::fetch_all_assessments() as $assessment) {
            $assessments[] = $assessment->toExternalForm();
        }

        return ["count" => count($assessments), "assessments" => $assessments];
    }

    public function get_acceptable_methods()
    {
        return ["GET"];
    }
}