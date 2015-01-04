<?php
namespace meteor\endpoints;

use meteor\core\Endpoint;
use meteor\database\Backend;

class GroupListEndpoint extends Endpoint
{
    public function handle($data)
    {
        $groups = array();

        foreach (Backend::fetch_all_groups() as $group) {
            $groups[] = $group->toExternalForm();
        }

        return array("count" => count($groups), "groups" => $groups);
    }

    public function get_acceptable_methods()
    {
        return array ("GET");
    }

}