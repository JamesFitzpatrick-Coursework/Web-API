<?php

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
}