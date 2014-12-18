<?php

class UserCreateEndpoint extends AuthenticatedEndpoint
{

    public function handle($data)
    {
        if (!isset($data->{"client-id"}) || !isset($data->{"username"}) || !isset($data->{"password"})) {
            throw new EndpointExecutionException("Invalid request");
        }

        $clientid = Token::decode($data->{"client-id"});
        $username = $data->{"username"};

        if (Backend::user_exists($username)) {
            throw new EndpointExecutionException("User already exists", array("username" => $username));
        }

        // Generate a new user id for this user
        $token = Token::generateNewToken(TOKEN_USER);

        // Hash their password for storage
        $password = Crypt::hashPassword($data->{"password"}, $token->getUserSecret());

        // Create their entry in the user database
        Backend::create_user($token, $username, $password);

        // Return the new user to the client
        return array(
            "user" => array ("user-id" => $token->toString(), "display-name" => $username)
        );
    }
}