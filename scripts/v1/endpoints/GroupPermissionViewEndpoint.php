<?php
namespace meteor\endpoints;

use meteor\database\Backend;

class GroupPermissionViewEndpoint extends AuthenticatedEndpoint
{
    public function handle($data)
    {
        $profile = Backend::fetch_group_profile($this->params["id"]);
        $permissions = Backend::fetch_group_permissions($profile);
        return array ("permissions" => $permissions);
    }

    public function get_acceptable_methods()
    {
        return array ("GET");
    }
}