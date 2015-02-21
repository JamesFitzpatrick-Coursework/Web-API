<?php
namespace meteor\endpoints\users;

use meteor\data\profiles\UserProfile;
use meteor\database\Backend;
use meteor\database\backend\UserBackend;
use meteor\endpoints\AuthenticatedEndpoint;

class UserListEndpoint extends AuthenticatedEndpoint
{
    public function handle($data)
    {
        $users = [];

        /** @var \meteor\data\profiles\UserProfile $user */
        foreach (UserBackend::fetch_all_users() as $user) {
            $users[] = $user->toExternalForm();
        }

        return ["count" => count($users), "users" => $users];
    }

    public function get_acceptable_methods()
    {
        return ["GET"];
    }
}