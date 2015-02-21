<?php
namespace meteor\endpoints\users;

use meteor\database\Backend;
use meteor\database\backend\UserBackend;
use meteor\endpoints\AuthenticatedEndpoint;

class UserPermissionEditEndpoint extends AuthenticatedEndpoint
{
    public function handle($data)
    {
        $this->validate_request(array ("permission"));

        $profile = UserBackend::fetch_user_profile($this->params["id"]);
        $permission = $data->{"permission"};

        UserBackend::set_user_permission($profile, $permission, true);

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