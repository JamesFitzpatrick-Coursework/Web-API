<?php
namespace meteor\endpoints;

use meteor\database\Backend;

class GroupSettingEditEndpoint extends AuthenticatedEndpoint
{
    public function handle($data)
    {
        $this->validate_request(array("setting" => array ("key", "value")));

        $profile = Backend::fetch_group_profile($this->params["id"]);
        $setting = $data->{"setting"};
        Backend::set_group_setting($profile, $setting);

        return array (
            "user" => $profile->toExternalForm(),
            "setting" => $setting
        );
    }

    public function get_acceptable_methods()
    {
        return array ("POST");
    }
}