<?php
namespace meteor\endpoints;

use meteor\database\Backend;

class UserPermissionLookupEndpoint extends AuthenticatedEndpoint
{
    public function handle($data)
    {
        $profile = Backend::fetch_user_profile($this->params["id"]);
        return array ("present" => Backend::check_user_permission($profile, $this->params["permission"]));
    }

    public function get_acceptable_methods()
    {
        return array ("GET");
    }
}