<?php
namespace meteor\endpoints;

use meteor\database\Backend;

class UserSettingEditEndpoint extends AuthenticatedEndpoint
{
    public function handle($data)
    {
        $state = $this->method == "POST";
        $this->validate_request(array("setting" => $state ? array ("key", "value") : array ("key")));

        $profile = Backend::fetch_user_profile($this->params["id"]);
        $setting = $data->{"setting"};

        if ($state) {
            Backend::set_user_setting($profile, $setting);
        } else {
            Backend::delete_user_setting($profile, $setting);
        }

        return array (
            "user" => $profile->toExternalForm(),
            "setting" => $setting
        );
    }

    public function get_acceptable_methods()
    {
        return array ("POST", "DELETE");
    }
}