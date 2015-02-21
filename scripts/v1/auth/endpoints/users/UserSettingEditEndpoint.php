<?php
namespace meteor\endpoints\users;

use meteor\database\Backend;
use meteor\database\backend\UserBackend;
use meteor\endpoints\AuthenticatedEndpoint;

class UserSettingEditEndpoint extends AuthenticatedEndpoint
{
    public function handle($data)
    {
        $this->validate_request(array("setting" => array ("key", "value")));

        $profile = UserBackend::fetch_user_profile($this->params["id"]);
        $setting = $data->{"setting"};

        UserBackend::set_user_setting($profile, $setting);

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