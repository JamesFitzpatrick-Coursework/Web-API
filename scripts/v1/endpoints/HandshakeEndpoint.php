<?php

class HandshakeEndpoint extends Endpoint
{

    public function handle($data)
    {
        if (!(isset($data->{"user-id"}) || isset($data->{"display-name"}))) {
            throw new EndpointExecutionException("Invalid request");
        }

        if (isset($data->{"user-id"})) {
            $userid = Token::decode($data->{"user-id"});
            $query = "SELECT `name` FROM " . DATABASE_TABLE_USERS . " WHERE `id`='" . $userid->toString() . "';";
            $result = Database::query($query);

            if (Database::count($result) == 0) {
                throw new EndpointExecutionException("Could not find a user with that id", array("user-iid" => $userid->toString()));
            }

            $displayname = Database::fetch_data($result)["name"];
        } elseif (isset($data->{"display-name"})) {
            $displayname = $data->{"display-name"};
            $query = "SELECT `id` FROM " . DATABASE_TABLE_USERS . " WHERE `name`='" . Database::format_string($displayname) . "';";
            $result = Database::query($query);

            if (Database::count($result) == 0) {
                throw new EndpointExecutionException("Could not find a user with that name", array("display-name" => $displayname));
            }

            $userid = Token::decode(Database::fetch_data($result)["id"]);
        }

        $clientid = Token::decode($data->{"client-id"});
        $token = Token::generateToken(TOKEN_REQUEST, $userid->getUserSecret());

        $query = "INSERT INTO " . DATABASE_TABLE_TOKENS . " (`token`, `client-id`, `user`, `expires`) VALUES ('" . $token->toString() . "','" . $clientid->toString() . "','" . $userid->toString() . "', NOW() + INTERVAL 1 HOUR);";
        $result = Database::query($query);

        if (!$result) {
            throw new EndpointExecutionException("An error occurred adding token to database", array("query" => $query));
        }

        return array(
            "user" => array("user-id" => $userid->toString(), "display-name" => $displayname),
            "request-token" => array("token" => $token->toString(), "expires" => 3600)
        );
    }
}