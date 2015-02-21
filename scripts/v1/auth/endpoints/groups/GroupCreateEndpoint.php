<?php
namespace meteor\endpoints\groups;

use meteor\database\Backend;
use common\exceptions\EndpointExecutionException;
use meteor\database\backend\GroupBackend;
use meteor\endpoints\AuthenticatedEndpoint;

class GroupCreateEndpoint extends AuthenticatedEndpoint
{
    public function handle($data)
    {
        $this->validate_request(array("group-name"));

        $groupname = $data->{"group-name"};
        $displayname = $groupname;

        if (isset($data->{"display-name"})) {
            $displayname = $data->{"display-name"};
        }

        if (GroupBackend::group_exists($groupname)) {
            throw new EndpointExecutionException("Group with name already exists", array ("group-name", $groupname));
        }

        // Add the group to the database
        $group = GroupBackend::create_group($groupname, $displayname);

        // Return the new user to the client
        return array(
            "group" => $group->toExternalForm()
        );

    }
}