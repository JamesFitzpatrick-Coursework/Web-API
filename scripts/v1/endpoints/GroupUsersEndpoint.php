<?php
namespace meteor\endpoints;

use meteor\data\UserProfile;
use meteor\database\Backend;

class GroupUsersEndpoint extends AuthenticatedEndpoint
{
    public function handle($data)
    {
        $group = Backend::fetch_group_profile($this->params['id']);
        $users = array();

        /** @var UserProfile $user */
        foreach (Backend::fetch_group_users($group) as $user) {
            $users[] = $user->toExternalForm();
        }

        return array("users" => $users);
    }

    public function get_acceptable_methods()
    {
        return array("GET");
    }
}