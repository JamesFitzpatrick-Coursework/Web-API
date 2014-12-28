<?php
/**
 * Created by PhpStorm.
 * User: James
 * Date: 15/12/2014
 * Time: 19:31
 */

class GroupCreateEndpoint extends Endpoint
{
    public function handle($data)
    {
        $this->validate_request($data, array ("group-name"));

        $groupname = $data->{"group-name"};

        if (Backend::group_exists($groupname)) {
            throw new EndpointExecutionException("Group with name already exists", array ("display-name", $groupname));
        }

        // Add the group to the database
        $groupid = Backend::create_group($groupname);

        // Return the new user to the client
        return array(
            "group" => array ("group-id" => $groupid->toString(), "display-name" => $groupname)
        );

    }
}