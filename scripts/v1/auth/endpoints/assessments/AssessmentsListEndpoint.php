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
        $assessments = array();

        /** @var AssessmentProfile $assessment */
        foreach (AssessmentBackend::fetch_all_assessments() as $assessment) {
            $assessments[] = $assessment->toExternalForm();
        }

        return array("count" => count($assessments), "users" => $assessments);
    }

    public function get_acceptable_methods()
    {
        return array ("GET");
    }
}