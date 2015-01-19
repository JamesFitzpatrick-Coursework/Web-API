<?php
namespace meteor\endpoints;

use meteor\data\GroupProfile;
use meteor\database\Backend;

class UserGroupsListEndpoint extends AuthenticatedEndpoint
{
    public function handle($data)
    {
        $profile = Backend::fetch_user_profile($this->params['id']);

        $groups = array();
        /** @var GroupProfile $group */
        foreach (Backend::fetch_user_groups($profile) as $group) {
            $groups[] = $group->toExternalForm();
        }

        return array ("groups" => $groups);
    }

    public function get_acceptable_methods()
    {
        return array ("GET");
    }
}