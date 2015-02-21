<?php
namespace meteor\endpoints\users;

use meteor\data\profiles\GroupProfile;
use meteor\database\Backend;
use meteor\database\backend\UserBackend;
use meteor\endpoints\AuthenticatedEndpoint;

class UserGroupsListEndpoint extends AuthenticatedEndpoint
{
    public function handle($data)
    {
        $profile = UserBackend::fetch_user_profile($this->params['id']);

        $groups = array();
        /** @var \meteor\data\profiles\GroupProfile $group */
        foreach (UserBackend::fetch_user_groups($profile) as $group) {
            $groups[] = $group->toExternalForm();
        }

        return array ("groups" => $groups);
    }

    public function get_acceptable_methods()
    {
        return array ("GET");
    }
}