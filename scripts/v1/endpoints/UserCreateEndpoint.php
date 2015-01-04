<?php
namespace meteor\endpoints;

use meteor\database\Backend;
use meteor\exceptions\EndpointExecutionException;

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

        if (Backend::user_exists($username)) {
            throw new EndpointExecutionException("User already exists", array("username" => $username));
        }

        // Create their entry in the user database
        $profile = Backend::create_user($username, $displayname, $data->{"password"});

        // Return the new user to the client
        return array(
            "user" => $profile->toExternalForm()
        );
    }
}