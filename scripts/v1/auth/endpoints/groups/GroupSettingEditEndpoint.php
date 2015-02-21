<?php
namespace meteor\endpoints\groups;

use meteor\database\Backend;
use meteor\database\backend\GroupBackend;
use meteor\endpoints\AuthenticatedEndpoint;

class GroupSettingEditEndpoint extends AuthenticatedEndpoint
{
    public function handle($data)
    {
        $this->validate_request(["setting" => ["key", "value"]]);

        $profile = GroupBackend::fetch_group_profile($this->params["id"]);
        $setting = $data->{"setting"};
        GroupBackend::set_group_setting($profile, $setting);

        return [
            "user"    => $profile->toExternalForm(),
            "setting" => $setting
        ];
    }

    public function get_acceptable_methods()
    {
        return ["POST"];
    }
}