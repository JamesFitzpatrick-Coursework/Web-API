<?php
namespace meteor\endpoints;

use meteor\database\Backend;
use common\exceptions\MethodNotAllowedException;

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

    public function handleGet($data) {
        $profile = Backend::fetch_user_profile($this->params["id"]);
        return array ("present" => Backend::check_user_permission($profile, $this->params["permission"]));
    }

    private function handlePost($data)
    {
        $profile = Backend::fetch_user_profile($this->params["id"]);
        Backend::set_user_permission($profile, $this->params["permission"], false);
        return array();
    }

    public function get_acceptable_methods()
    {
        return array ("GET", "DELETE");
    }
}