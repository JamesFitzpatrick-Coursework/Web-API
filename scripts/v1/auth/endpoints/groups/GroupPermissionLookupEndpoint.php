<?php
namespace meteor\endpoints\groups;

use meteor\database\Backend;
use common\exceptions\MethodNotAllowedException;
use meteor\database\backend\GroupBackend;
use meteor\endpoints\AuthenticatedEndpoint;

class GroupPermissionLookupEndpoint extends AuthenticatedEndpoint
{
    public function handle($data)
    {
        if ($this->method == "GET") {
            return $this->handleGet($data);
        } else if ($this->method == "DELETE") {
            return $this->handleDelete($data);
        } else {
            throw new MethodNotAllowedException($this->method);
        }
    }

    public function handleGet($data)
    {
        $profile = GroupBackend::fetch_group_profile($this->params["id"]);
        return array ("present" => GroupBackend::check_group_permission($profile, $this->params["permission"]));
    }

    public function handleDelete($data)
    {
        $profile = GroupBackend::fetch_group_profile($this->params["id"]);
        GroupBackend::set_group_permission($profile, $this->params["permission"], true);
        return array();
    }


    public function get_acceptable_methods()
    {
        return array ("GET", "DELETE");
    }
}