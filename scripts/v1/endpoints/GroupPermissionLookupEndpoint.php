<?php
namespace meteor\endpoints;

use meteor\database\Backend;

class GroupPermissionLookupEndpoint extends AuthenticatedEndpoint
{
    public function handle($data)
    {
        $profile = Backend::fetch_group_profile($this->params["id"]);
        return array ("present" => Backend::check_group_permission($profile, $this->params["permission"]));
    }

    public function get_acceptable_methods()
    {
        return array ("GET");
    }
}