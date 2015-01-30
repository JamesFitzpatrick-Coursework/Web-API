<?php
namespace meteor\endpoints;

use meteor\database\Backend;

class UserGroupAddEndpoint extends AuthenticatedEndpoint
{
    public function handle($data)
    {
        $this->validate_request(array ("group"));

        $user = Backend::fetch_user_profile($this->params['id']);
        $group = Backend::fetch_group_profile($data->{"group"});
        Backend::add_user_group($user, $group);
        return array();
    }
}