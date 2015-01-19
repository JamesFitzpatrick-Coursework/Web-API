<?php
namespace meteor\endpoints;

use meteor\database\Backend;

class GroupPermissionEditEndpoint extends AuthenticatedEndpoint
{
    public function handle($data)
    {
        $this->validate_request(array ("permission"));

        $profile = Backend::fetch_group_profile($this->params["id"]);
        $permission = $data->{"permission"};
        $state = $this->method == "POST";

        Backend::set_group_permission($profile, $permission, $state);

        return array (
            "user" => $profile->toExternalForm(),
            "permission" => $permission,
            "state" => $state ? "ADDED" : "DELETED"
        );
    }

    public function get_acceptable_methods()
    {
        return array ("POST", "DELETE");
    }
}