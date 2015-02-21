<?php
namespace meteor\endpoints\users;

use meteor\database\Backend;
use common\exceptions\MethodNotAllowedException;
use meteor\database\backend\UserBackend;
use meteor\endpoints\AuthenticatedEndpoint;

class UserSettingLookupEndpoint extends AuthenticatedEndpoint
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
        $profile = UserBackend::fetch_user_profile($this->params["id"]);
        $setting = $this->params["setting"];

        return array (
            "setting" => array ("key" => $setting, "value" => UserBackend::fetch_user_setting($profile, $setting))
        );
    }

    private function handlePost($data)
    {
        $profile = UserBackend::fetch_user_profile($this->params["id"]);
        UserBackend::delete_user_setting($profile, $this->params["setting"], false);
        return array();
    }

    public function get_acceptable_methods()
    {
        return array ("GET", "DELETE");
    }
}