<?php
namespace meteor\endpoints\groups;

use common\data\Token;
use meteor\data\profiles\UserProfile;
use meteor\database\backend\AssignmentBackend;
use meteor\database\backend\GroupBackend;
use meteor\database\backend\UserBackend;
use meteor\endpoints\AuthenticatedEndpoint;

class GroupLookupAssignmentEndpoint extends AuthenticatedEndpoint
{
    public function handle($data)
    {
        $group = GroupBackend::fetch_group_profile($this->params['id']);
        $assignment = AssignmentBackend::fetch_assignment_profile(Token::decode($this->params['assignment']));
        $data = [];

        /** @var UserProfile $user */
        foreach (GroupBackend::fetch_group_users($group) as $user) {
            $data[] = [
                    "user" => $user->toExternalForm(),
                    "score" => UserBackend::fetch_user_scores($user, $assignment)
                ];
        }

        return ["users" => $data];
    }

    public function get_acceptable_methods()
    {
        return [ "GET" ];
    }
} 