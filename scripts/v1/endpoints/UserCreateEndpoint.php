<?php

class UserCreateEndpoint extends AuthenticatedEndpoint
{

    public function handle($data)
    {
        if (!isset($data->{"username"}) || !isset($data->{"password"})) {
            throw new EndpointExecutionException("Invalid request");
        }

        $username = $data->{"username"};

        if (Backend::user_exists($username)) {
            throw new EndpointExecutionException("User already exists", array("username" => $username));
        }

        // Create their entry in the user database
        $userid = Backend::create_user($username, $data->{"password"});

        // Return the new user to the client
        return array(
            "user" => array ("user-id" => $userid->toString(), "display-name" => $username)
        );
    }
}