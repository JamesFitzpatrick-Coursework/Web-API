<?php
namespace meteor\endpoints;

use meteor\database\Backend;

class UserListEndpoint extends AuthenticatedEndpoint
{
    public function handle($data)
    {
        $users = array();

        foreach (Backend::fetch_all_users() as $user) {
            $users[] = $user->toExternalForm();
        }

        return array("count" => count($users), "users" => $users);
    }

    public function get_acceptable_methods()
    {
        return array ("GET");
    }
}