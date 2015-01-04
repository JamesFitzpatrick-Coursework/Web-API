<?php
namespace meteor\endpoints;

use meteor\database\Backend;

class GroupLookupEndpoint extends AuthenticatedEndpoint
{
    public function handle($data)
    {
        $profile = Backend::fetch_group_profile($this->params["id"]);

        $data = array ();
        $data["profile"] = $profile->toExternalForm();
        $data["settings"] = Backend::fetch_group_settings($profile);
        $data["permissions"] = Backend::fetch_group_permissions($profile);

        return $data;
    }

    public function get_acceptable_methods()
    {
        return array ("GET");
    }
}