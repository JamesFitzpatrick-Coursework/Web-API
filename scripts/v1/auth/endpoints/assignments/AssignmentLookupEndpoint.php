<?php
namespace meteor\endpoints\assignments;

use common\data\Token;
use meteor\data\Assessment;
use meteor\data\profiles\AssessmentProfile;
use meteor\database\Backend;
use meteor\database\backend\AssignmentBackend;
use meteor\endpoints\AuthenticatedEndpoint;

class AssignmentLookupEndpoint extends AuthenticatedEndpoint
{

    public function handle($data)
    {
        if ($this->method == "GET") {
            return $this->handleGet($data);
        } elseif ($this->method == "DELETE") {
            return $this->handleDelete($data);
        }

        return [];
    }

    public function handleDelete($data)
    {
        AssignmentBackend::delete_assignment(Token::decode($this->params['id']));
        return [];
    }

    public function handleGet($data)
    {
        $assignment = AssignmentBackend::fetch_assignment_profile(Token::decode($this->params['id']));

        return [
            "assignment" => $assignment
        ];
    }

    public function get_acceptable_methods()
    {
        return array("GET", "DELETE");
    }
}