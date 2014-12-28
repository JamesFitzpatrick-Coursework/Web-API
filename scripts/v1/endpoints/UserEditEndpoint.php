<?php
/**
 * Created by PhpStorm.
 * User: James
 * Date: 28/12/2014
 * Time: 13:35
 */

class UserEditEndpoint extends Endpoint {

    public function handle($data)
    {
        $this->validate_request($data, array ("user", "setting"));

        $profile = Backend::fetch_user_profile($data->{"user"});
        $setting = $data->{"setting"};

        $this->validate_request($setting, array ("key", "value"));

        Backend::set_user_setting($profile, $setting);

        return array (
            "user" => $profile->toExternalForm(),
            "setting" => $setting
        );
    }
}