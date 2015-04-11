<?php
namespace meteor\endpoints\groups;

use common\data\Token;
use meteor\database\backend\AssignmentBackend;
use meteor\database\backend\GroupBackend;
use meteor\database\backend\UserBackend;
use meteor\endpoints\AuthenticatedEndpoint;

class GroupAddAssignmentEndpoint extends AuthenticatedEndpoint
{
    public function handle($data)
    {
        $this->validate_request([
            "assignment"
        ]);

        $group = GroupBackend::fetch_group_profile($this->params['id']);
        $users = GroupBackend::fetch_group_users($group);
        $assignmentId = Token::decode($data->{"assignment"});

        foreach ($users as $user) {
            UserBackend::add_user_assignment($user, $assignmentId);
        }

        $assignmentId = AssignmentBackend::fetch_assignment_profile($assignmentId);

        return [ "assignment" => $assignmentId];
    }
}