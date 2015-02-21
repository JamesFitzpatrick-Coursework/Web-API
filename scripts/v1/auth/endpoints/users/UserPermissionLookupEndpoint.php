<?php
namespace meteor\endpoints\users;

use common\exceptions\MethodNotAllowedException;
use meteor\database\Backend;
use meteor\database\backend\UserBackend;
use meteor\endpoints\AuthenticatedEndpoint;

class UserPermissionLookupEndpoint extends AuthenticatedEndpoint
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

        return ["present" => UserBackend::check_user_permission($profile, $this->params["permission"])];
    }

    private function handlePost($data)
    {
        $profile = UserBackend::fetch_user_profile($this->params["id"]);
        UserBackend::set_user_permission($profile, $this->params["permission"], false);

        return [];
    }

    public function get_acceptable_methods()
    {
        return ["GET", "DELETE"];
    }
}