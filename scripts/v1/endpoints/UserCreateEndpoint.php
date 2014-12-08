<?php

class UserCreateEndpoint extends Endpoint
{

    public function handle($body)
    {
        $data = json_decode($body);

        if (!isset($data->{"client-id"}) || !isset($data->{"username"}) || !isset($data->{"password"})) {
            throw new EndpointExecutionException("Invalid request");
        }

        $clientid = Token::decode($data->{"client-id"});
        $username = $data->{"username"};

        // Check to see if user exists already
        $result = Database::query("SELECT count('id') AS `count` FROM " . DATABASE_TABLE_USERS . " WHERE `name`='" . $username . "'");
        $count = Database::fetch_data($result)["count"];
        Database::close_query($result);

        if ($count >= 1) {
            throw new EndpointExecutionException("User already exists", array("username" => $username));
        }

        // Generate a new user id for this user
        $token = Token::generateNewToken(TOKEN_USER);

        // Hash their password for storage
        $password = Crypt::hashPassword($data->{"password"}, $token->getUserSecret());

        // Add the user to the database
        Database::query("INSERT INTO " . DATABASE_TABLE_USERS . " VALUES
						('" . $token->toString() . "',
						'" . Database::formatString($username) . "',
						'" . $token->getUserSecret() . "',
						'" . Database::formatString($password) . "');");

        // Return the new user to the client
        return array(
            "client-id" => $clientid->toString(),
            "userid" => $token->toString(),
            "username" => $username
        );
    }
}