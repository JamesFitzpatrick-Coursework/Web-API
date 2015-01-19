<?php
namespace meteor\endpoints;

use meteor\database\Backend;
use common\exceptions\MethodNotAllowedException;

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
        $profile = Backend::fetch_group_profile($this->params["id"]);
        return array ("present" => Backend::check_group_permission($profile, $this->params["permission"]));
    }

    public function handleDelete($data)
    {
        $profile = Backend::fetch_group_profile($this->params["id"]);
        Backend::set_group_permission($profile, $this->params["permission"], true);
        return array();
    }


    public function get_acceptable_methods()
    {
        return array ("GET", "DELETE");
    }
}