<?php
namespace meteor\endpoints\users;

use meteor\database\Backend;
use meteor\database\backend\UserBackend;
use meteor\endpoints\AuthenticatedEndpoint;

class UserPermissionViewEndpoint extends AuthenticatedEndpoint
{
    public function handle($data)
    {
        $profile = UserBackend::fetch_user_profile($this->params["id"]);
        $permissions = UserBackend::fetch_user_permissions($profile);
        return array ("permissions" => $permissions);
    }

    public function get_acceptable_methods()
    {
        return array ("GET");
    }
}