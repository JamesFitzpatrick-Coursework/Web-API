<?php
namespace meteor\endpoints\users;

use common\data\Token;
use meteor\database\backend\UserBackend;
use meteor\endpoints\AuthenticatedEndpoint;

class UserAddAssignmentEndpoint extends AuthenticatedEndpoint
{
    public function handle($data)
    {
        $this->validate_request([
            "assignment"
        ]);

        $user = UserBackend::fetch_user_profile($this->params['id']);
        $assignment = Token::decode($data->{"assignment"});
        $data = UserBackend::add_user_assignment($user, $assignment);

        return $data;
    }
}