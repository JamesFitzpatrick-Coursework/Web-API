<?php
namespace meteor\endpoints;

use meteor\database\Backend;

class UserSettingLookupEndpoint extends AuthenticatedEndpoint
{
    public function handle($data)
    {
        $profile = Backend::fetch_user_profile($this->params["id"]);
        $setting = $this->params["setting"];

        return array (
            "setting" => array ("key" => $setting, "value" => Backend::fetch_user_setting($profile, $setting))
        );
    }

    public function get_acceptable_methods()
    {
        return array ("GET");
    }
}