<?php
namespace meteor\endpoints\groups;

use meteor\database\Backend;
use meteor\database\backend\GroupBackend;
use meteor\endpoints\AuthenticatedEndpoint;

class GroupPermissionViewEndpoint extends AuthenticatedEndpoint
{
    public function handle($data)
    {
        $profile = GroupBackend::fetch_group_profile($this->params["id"]);
        $permissions = GroupBackend::fetch_group_permissions($profile);
        return array ("permissions" => $permissions);
    }

    public function get_acceptable_methods()
    {
        return array ("GET");
    }
}