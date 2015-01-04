<?php
namespace meteor\endpoints;

use meteor\database\Backend;
use meteor\exceptions\EndpointExecutionException;

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

        if (Backend::group_exists($groupname)) {
            throw new EndpointExecutionException("Group with name already exists", array ("group-name", $groupname));
        }

        // Add the group to the database
        $group = Backend::create_group($groupname, $displayname);

        // Return the new user to the client
        return array(
            "group" => $group->toExternalForm()
        );

    }
}