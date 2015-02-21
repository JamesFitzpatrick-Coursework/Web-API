<?php
namespace meteor\endpoints\groups;

use meteor\data\profiles\UserProfile;
use meteor\database\Backend;
use meteor\database\backend\GroupBackend;
use meteor\endpoints\AuthenticatedEndpoint;

class GroupUsersEndpoint extends AuthenticatedEndpoint
{
    public function handle($data)
    {
        $group = GroupBackend::fetch_group_profile($this->params['id']);
        $users = [];

        /** @var \meteor\data\profiles\UserProfile $user */
        foreach (GroupBackend::fetch_group_users($group) as $user) {
            $users[] = $user->toExternalForm();
        }

        return ["users" => $users];
    }

    public function get_acceptable_methods()
    {
        return ["GET"];
    }
}