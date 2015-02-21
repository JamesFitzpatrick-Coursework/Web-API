<?php
namespace meteor\endpoints\groups;

use meteor\database\Backend;
use meteor\database\backend\GroupBackend;
use meteor\endpoints\AuthenticatedEndpoint;

class GroupPermissionEditEndpoint extends AuthenticatedEndpoint
{
    public function handle($data)
    {
        $this->validate_request(array ("permission"));

        $profile = GroupBackend::fetch_group_profile($this->params["id"]);
        $permission = $data->{"permission"};

        GroupBackend::set_group_permission($profile, $permission, true);

        return array (
            "user" => $profile->toExternalForm(),
            "permission" => $permission
        );
    }

    public function get_acceptable_methods()
    {
        return array ("POST");
    }
}