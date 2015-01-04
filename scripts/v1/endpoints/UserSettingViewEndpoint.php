<?php
namespace meteor\endpoints;

use meteor\database\Backend;

class UserSettingViewEndpoint extends AuthenticatedEndpoint
{
    public function handle($data)
    {
        $profile = Backend::fetch_user_profile($this->params["id"]);
        $settings = Backend::fetch_user_settings($profile);
        return array ("settings" => $settings);
    }

    public function get_acceptable_methods()
    {
        return array ("GET");
    }
}