<?php
namespace meteor\endpoints\users;

use meteor\database\Backend;
use common\exceptions\EndpointExecutionException;
use meteor\database\backend\UserBackend;
use meteor\endpoints\AuthenticatedEndpoint;

class UserCreateEndpoint extends AuthenticatedEndpoint
{
    public function handle($data)
    {
        $this->validate_request(array("username", "password"));
        $username = $data->{"username"};
        $displayname = $username;

        if (isset($data->{"display-name"})) {
            $displayname = $data->{"display-name"};
        }

        if (UserBackend::user_exists($username)) {
            throw new EndpointExecutionException("User already exists", array("username" => $username));
        }

        // Create their entry in the user database
        $profile = UserBackend::create_user($username, $displayname, $data->{"password"});

        // Return the new user to the client
        return array(
            "user" => $profile->toExternalForm()
        );
    }
}