<?php
namespace meteor\endpoints;

use meteor\database\Backend;
use meteor\database\backend\AssessmentBackend;

class AssessmentsListEndpoint extends AuthenticatedEndpoint
{
    public function handle($data)
    {
        return [
            "assessments" => AssessmentBackend::fetch_all_assessments()
        ];
    }

    public function get_acceptable_methods()
    {
        return ["GET"];
    }
}