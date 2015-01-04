<?php
namespace meteor\endpoints;

use meteor\database\Backend;

class GroupSettingViewEndpoint extends AuthenticatedEndpoint
{
    public function handle($data)
    {
        $profile = Backend::fetch_group_profile($this->params["id"]);
        $settings = Backend::fetch_group_settings($profile);
        return array ("settings" => $settings);
    }

    public function get_acceptable_methods()
    {
        return array ("GET");
    }
}