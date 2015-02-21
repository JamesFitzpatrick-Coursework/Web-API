<?php
namespace meteor\endpoints\groups;

use meteor\database\Backend;
use common\exceptions\MethodNotAllowedException;
use meteor\database\backend\GroupBackend;
use meteor\endpoints\AuthenticatedEndpoint;

class GroupSettingLookupEndpoint extends AuthenticatedEndpoint
{
    public function handle($data)
    {
        if ($this->method == "GET") {
            return $this->handleGet($data);
        } else if ($this->method == "DELETE") {
            return $this->handlePost($data);
        } else {
            throw new MethodNotAllowedException($this->method);
        }
    }

    public function handleGet($data)
    {
        $profile = GroupBackend::fetch_group_profile($this->params["id"]);
        $setting = $this->params["setting"];

        return array (
            "setting" => array ("key" => $setting, "value" => GroupBackend::fetch_group_setting($profile, $setting))
        );
    }

    private function handlePost($data)
    {
        $profile = GroupBackend::fetch_group_profile($this->params["id"]);
        GroupBackend::delete_group_setting($profile, $this->params["setting"]);
        return array();
    }

    public function get_acceptable_methods()
    {
        return array ("GET", "DELETE");
    }
}