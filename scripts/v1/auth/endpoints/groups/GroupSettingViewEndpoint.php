<?php
namespace meteor\endpoints\groups;

use meteor\database\Backend;
use meteor\database\backend\GroupBackend;
use meteor\endpoints\AuthenticatedEndpoint;

class GroupSettingViewEndpoint extends AuthenticatedEndpoint
{
    public function handle($data)
    {
        $profile = GroupBackend::fetch_group_profile($this->params["id"]);
        $settings = GroupBackend::fetch_group_settings($profile);

        return ["settings" => $settings];
    }

    public function get_acceptable_methods()
    {
        return ["GET"];
    }
}