<?php
namespace meteor\endpoints\assignments;

use meteor\data\profiles\AssessmentProfile;
use meteor\database\Backend;
use meteor\database\backend\AssignmentBackend;
use meteor\endpoints\AuthenticatedEndpoint;

class AssignmentsListEndpoint extends AuthenticatedEndpoint
{
    public function handle($data)
    {
        $assignments = AssignmentBackend::fetch_all_assignments();
        return array("count" => count($assignments), "assignments" => $assignments);
    }

    public function get_acceptable_methods()
    {
        return array ("GET");
    }
}