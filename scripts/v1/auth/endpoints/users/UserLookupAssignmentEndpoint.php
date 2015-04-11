<?php
namespace meteor\endpoints\users;

use common\data\Token;
use meteor\database\backend\AssignmentBackend;
use meteor\database\backend\UserBackend;
use meteor\endpoints\AuthenticatedEndpoint;

class UserLookupAssignmentEndpoint extends AuthenticatedEndpoint
{
    public function handle($data)
    {
        $user = UserBackend::fetch_user_profile($this->params['id']);
        $assignment = AssignmentBackend::fetch_assignment_profile(Token::decode($this->params['assignment']));

        return UserBackend::fetch_user_scores($user, $assignment);
    }

    public function get_acceptable_methods()
    {
        return [ "GET" ];
    }
}