<?php
namespace meteor\endpoints\users;

use meteor\database\Backend;
use meteor\database\backend\UserBackend;
use meteor\endpoints\AuthenticatedEndpoint;

class UserSettingViewEndpoint extends AuthenticatedEndpoint
{
    public function handle($data)
    {
        $profile = UserBackend::fetch_user_profile($this->params["id"]);
        $settings = UserBackend::fetch_user_settings($profile);

        return ["settings" => $settings];
    }

    public function get_acceptable_methods()
    {
        return ["GET"];
    }
}