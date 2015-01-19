<?php
namespace meteor\endpoints;

use common\core\Endpoint;
use meteor\data\GroupProfile;
use meteor\database\Backend;

class GroupListEndpoint extends Endpoint
{
    public function handle($data)
    {
        $groups = array();

        /** @var GroupProfile $group */
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