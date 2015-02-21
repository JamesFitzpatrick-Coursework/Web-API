<?php
namespace meteor\endpoints\users;

use meteor\database\Backend;
use meteor\database\backend\GroupBackend;
use meteor\database\backend\UserBackend;
use meteor\endpoints\AuthenticatedEndpoint;

class UserGroupAddEndpoint extends AuthenticatedEndpoint
{
    public function handle($data)
    {
        $this->validate_request(array ("group"));

        $user = UserBackend::fetch_user_profile($this->params['id']);
        $group = GroupBackend::fetch_group_profile($data->{"group"});
        UserBackend::add_user_group($user, $group);
        return array();
    }
}