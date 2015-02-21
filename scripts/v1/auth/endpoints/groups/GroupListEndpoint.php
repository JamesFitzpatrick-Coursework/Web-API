<?php
namespace meteor\endpoints\groups;

use common\core\Endpoint;
use meteor\data\profiles\GroupProfile;
use meteor\database\Backend;
use meteor\database\backend\GroupBackend;

class GroupListEndpoint extends Endpoint
{
    public function handle($data)
    {
        $groups = [];

        /** @var GroupProfile $group */
        foreach (GroupBackend::fetch_all_groups() as $group) {
            $groups[] = $group->toExternalForm();
        }

        return ["count" => count($groups), "groups" => $groups];
    }

    public function get_acceptable_methods()
    {
        return ["GET"];
    }

}