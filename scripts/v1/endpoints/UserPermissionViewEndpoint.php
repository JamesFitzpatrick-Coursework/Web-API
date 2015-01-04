<?php
namespace meteor\endpoints;

use meteor\database\Backend;

class UserPermissionViewEndpoint extends AuthenticatedEndpoint
{
    public function handle($data)
    {
        $profile = Backend::fetch_user_profile($this->params["id"]);
        $permissions = Backend::fetch_user_permissions($profile);
        return array ("permissions" => $permissions);
    }

    public function get_acceptable_methods()
    {
        return array ("GET");
    }
}