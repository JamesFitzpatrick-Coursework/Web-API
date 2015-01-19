<?php
namespace meteor\endpoints;

use meteor\database\Backend;

class UserPermissionEditEndpoint extends AuthenticatedEndpoint
{
    public function handle($data)
    {
        $this->validate_request(array ("permission"));

        $profile = Backend::fetch_user_profile($this->params["id"]);
        $permission = $data->{"permission"};

        Backend::set_user_permission($profile, $permission, true);

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